<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class BookingsController
{
    public function index(Request $request): JsonResponse
    {
        $status = strtolower((string)$request->query('status', 'all'));
        $page = max(1, (int)$request->query('page', 1));
        $limit = max(1, min(100, (int)$request->query('limit', 10)));
        $offset = ($page - 1) * $limit;
        $q = DB::table('bookings');
        if ($status !== 'all') $q->whereRaw('LOWER(status) = ?', [$status]);
        $total = (int)$q->count();
        $items = $q->orderByDesc('id')->limit($limit)->offset($offset)->get()->toArray();
        return response()->json(['success' => true, 'items' => $items, 'page' => $page, 'limit' => $limit, 'total' => $total]);
    }

    public function store(Request $request): JsonResponse
    {
        $clientId = (int)$request->input('client_id');
        $providerId = (int)$request->input('provider_id');
        $service = trim((string)$request->input('service_name'));
        $date = trim((string)$request->input('scheduled_date'));
        $time = trim((string)$request->input('scheduled_time'));
        $price = (float)$request->input('price');
        DB::table('bookings')->insert(['client_id' => $clientId, 'provider_id' => $providerId, 'service_name' => $service, 'scheduled_date' => $date, 'scheduled_time' => $time, 'price' => $price, 'status' => 'pending', 'created_at' => now(), 'updated_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function show(int $id): JsonResponse
    {
        $row = DB::table('bookings')->where('id', $id)->first();
        return response()->json(['success' => (bool)$row, 'item' => $row]);
    }

    public function cancel(int $id): JsonResponse
    {
        DB::table('bookings')->where('id', $id)->update(['status' => 'cancelled', 'updated_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $status = trim((string)$request->input('status'));
        DB::table('bookings')->where('id', $id)->update(['status' => $status, 'updated_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function rate(Request $request, int $id): JsonResponse
    {
        $rating = (float)$request->input('rating');
        DB::table('bookings')->where('id', $id)->update(['rating' => $rating, 'updated_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function requestReturn(int $id): JsonResponse
    {
        DB::table('bookings')->where('id', $id)->update(['status' => 'return', 'updated_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function listReturns(): JsonResponse
    {
        $rows = DB::table('bookings')->where('status', 'return')->orderByDesc('id')->get()->toArray();
        return response()->json(['success' => true, 'items' => $rows]);
    }
}