<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    // List bookings for the authenticated user or provider
    public function index(Request $request)
    {
        $user = $request->user();
        $roleName = strtolower($user->role->name ?? '');
        $status = $request->query('status');

        $query = Booking::query()->with(['provider', 'user']);

        if ($roleName === 'provider') {
            $providerId = $user->providerDetails->id ?? null;
            if (!$providerId) {
                return response()->json(['success' => false, 'message' => 'No provider profile found'], 403);
            }
            $query->where('provider_id', $providerId);
        } else {
            $query->where('user_id', $user->id);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $bookings = $query->orderByDesc('id')->paginate(20);
        return response()->json(['success' => true, 'data' => $bookings]);
    }

    // Create a new booking (client action)
    public function store(Request $request)
    {
        $user = $request->user();
        $data = $request->all();
        // In local/dev environment, allow any integer provider_id to unblock UI flows.
        // In other environments, require a valid provider record.
        $providerRule = app()->environment('local') ? 'required|integer' : 'required|exists:service_providers,id';
        $validator = Validator::make($data, [
            'provider_id' => $providerRule,
            'service_name' => 'required|string|max:255',
            'scheduled_date' => 'nullable|date',
            'scheduled_time' => 'nullable|date_format:H:i',
            'address' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $booking = Booking::create([
            'user_id' => $user->id,
            'provider_id' => (int) $data['provider_id'],
            'service_name' => $data['service_name'],
            'scheduled_date' => $data['scheduled_date'] ?? null,
            'scheduled_time' => $data['scheduled_time'] ?? null,
            'address' => $data['address'] ?? null,
            'status' => Booking::STATUS_PENDING,
            'price' => $data['price'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        $booking->load(['provider', 'user']);
        try {
            $socketBase = env('SOCKET_URL', 'http://localhost:3000');
            $providerUserId = optional($booking->provider)->user_id;
            Http::post(rtrim($socketBase, '/').'/notify', [
                'type' => 'booking.created',
                'title' => 'New Booking',
                'body' => 'You have a new booking request',
                'booking_id' => $booking->id,
                'to_user_id' => $providerUserId,
                'to_role' => 'provider',
                'data' => [
                    'booking_id' => $booking->id,
                    'service_name' => $booking->service_name,
                    'client_name' => optional($booking->user)->name,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('Notify booking.created failed: '.$e->getMessage());
        }
        return response()->json(['success' => true, 'data' => $booking], 201);
    }

    // Show a specific booking if authorized
    public function show(Request $request, int $id)
    {
        $user = $request->user();
        $roleName = strtolower($user->role->name ?? '');
        $booking = Booking::with(['provider', 'user'])->findOrFail($id);

        $isOwner = $booking->user_id === $user->id;
        $isProvider = false;
        if ($roleName === 'provider') {
            $providerId = $user->providerDetails->id ?? null;
            $isProvider = $providerId && $booking->provider_id === $providerId;
        }
        if (!$isOwner && !$isProvider) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }
        return response()->json(['success' => true, 'data' => $booking]);
    }

    // Update booking status (provider action for progress)
    public function updateStatus(Request $request, int $id)
    {
        $user = $request->user();
        $roleName = strtolower($user->role->name ?? '');
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:pending,ongoing,completed',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        $booking = Booking::findOrFail($id);

        // Only the associated provider can update progress
        if ($roleName !== 'provider' || ($user->providerDetails->id ?? null) !== $booking->provider_id) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $newStatus = $request->input('status');
        $allowed = [Booking::STATUS_PENDING, Booking::STATUS_ONGOING, Booking::STATUS_COMPLETED];
        if (!in_array($newStatus, $allowed, true)) {
            return response()->json(['success' => false, 'message' => 'Invalid status'], 422);
        }

        $booking->status = $newStatus;
        if ($newStatus === Booking::STATUS_COMPLETED) {
            $booking->completed_at = now();
        }
        $booking->save();

        $booking->refresh();
        try {
            $socketBase = env('SOCKET_URL', 'http://localhost:3000');
            $payload = [
                'booking_id' => $booking->id,
                'to_user_id' => $booking->user_id,
                'to_role' => 'client',
                'data' => [ 'booking_id' => $booking->id, 'status' => $newStatus ],
            ];
            if ($newStatus === Booking::STATUS_ONGOING) {
                $payload['type'] = 'booking.ongoing';
                $payload['title'] = 'Booking Accepted';
                $payload['body'] = 'Your booking is now ongoing.';
            } elseif ($newStatus === Booking::STATUS_COMPLETED) {
                $payload['type'] = 'booking.completed';
                $payload['title'] = 'Booking Completed';
                $payload['body'] = 'Your booking has been marked completed.';
            } else {
                $payload['type'] = 'booking.updated';
                $payload['title'] = 'Booking Updated';
                $payload['body'] = 'Your booking status changed.';
            }
            Http::post(rtrim($socketBase, '/').'/notify', $payload);
        } catch (\Throwable $e) {
            Log::warning('Notify booking.updateStatus failed: '.$e->getMessage());
        }
        return response()->json(['success' => true, 'data' => $booking]);
    }

    // Cancel a booking (client action)
    public function cancel(Request $request, int $id)
    {
        $user = $request->user();
        $booking = Booking::findOrFail($id);
        if ($booking->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        if (in_array($booking->status, [Booking::STATUS_COMPLETED, Booking::STATUS_CANCELLED], true)) {
            return response()->json(['success' => false, 'message' => 'Cannot cancel completed/cancelled booking'], 422);
        }

        $booking->status = Booking::STATUS_CANCELLED;
        $booking->cancelled_at = now();
        $booking->save();
        try {
            $socketBase = env('SOCKET_URL', 'http://localhost:3000');
            $provider = ServiceProvider::find($booking->provider_id);
            $providerUserId = optional($provider)->user_id;
            Http::post(rtrim($socketBase, '/').'/notify', [
                'type' => 'booking.cancelled',
                'title' => 'Booking Cancelled',
                'body' => 'A client cancelled their booking.',
                'booking_id' => $booking->id,
                'to_user_id' => $providerUserId,
                'to_role' => 'provider',
                'data' => [ 'booking_id' => $booking->id ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('Notify booking.cancel failed: '.$e->getMessage());
        }
        return response()->json(['success' => true, 'data' => $booking]);
    }

    // Rate a completed booking (client action)
    public function rate(Request $request, int $id)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $booking = Booking::findOrFail($id);
        if ($booking->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }
        if ($booking->status !== Booking::STATUS_COMPLETED) {
            return response()->json(['success' => false, 'message' => 'Only completed bookings can be rated'], 422);
        }

        $booking->rating = (int) $request->input('rating');
        $booking->rated_at = now();
        $booking->save();

        // Optionally update provider's average rating
        $provider = ServiceProvider::find($booking->provider_id);
        if ($provider) {
            $avg = Booking::where('provider_id', $provider->id)
                ->whereNotNull('rating')
                ->avg('rating');
            if ($avg !== null) {
                $provider->rating = round($avg, 2);
                $provider->save();
            }
        }

        $booking->refresh();
        return response()->json(['success' => true, 'data' => $booking]);
    }

    // Public: Check availability for a given date
    public function availability(Request $request)
    {
        $dateStr = $request->query('date');
        try {
            $d = $dateStr ? new \DateTime($dateStr) : new \DateTime();
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date',
            ], 422);
        }
        // Fully booked on weekends (Sat/Sun). Otherwise available with 30-min slots 08:00-20:00.
        $dayOfWeek = (int) $d->format('w'); // 0=Sun,6=Sat
        $available = ($dayOfWeek !== 0 && $dayOfWeek !== 6);
        $slots = [];
        if ($available) {
            $start = new \DateTime($d->format('Y-m-d').' 08:00:00');
            $end = new \DateTime($d->format('Y-m-d').' 20:00:00');
            $cursor = clone $start;
            while ($cursor <= $end) {
                $slots[] = $cursor->format('g:i A');
                $cursor->modify('+30 minutes');
            }
        }
        return response()->json([
            'success' => true,
            'available' => $available,
            'slots' => $slots,
        ]);
    }
}
