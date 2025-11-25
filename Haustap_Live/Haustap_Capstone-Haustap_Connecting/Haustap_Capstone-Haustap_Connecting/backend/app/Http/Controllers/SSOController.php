<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SSOController extends Controller
{
    /**
     * Establish a Laravel session for an admin coming from the PHP admin panel.
     * Accepts GET or POST with: email, ts, sig (HMAC SHA-256 of "email|ts"), optional name.
     * Signature uses secret from config('services.admin_sso.secret').
     */
    public function admin(Request $request)
    {
        $email = $request->input('email', $request->query('email'));
        $name = $request->input('name', $request->query('name')) ?? 'System Admin';
        $ts = (int) ($request->input('ts', $request->query('ts')) ?? 0);
        $sig = $request->input('sig', $request->query('sig'));

        $secret = config('services.admin_sso.secret');

        if (!$email || !$ts || !$sig || !$secret) {
            return response()->json(['success' => false, 'message' => 'Invalid payload'], 400);
        }

        // Basic replay window (5 minutes)
        if (abs(time() - $ts) > 300) {
            return response()->json(['success' => false, 'message' => 'Expired token'], 400);
        }

        $expected = hash_hmac('sha256', $email . '|' . $ts, $secret);
        if (!hash_equals($expected, $sig)) {
            return response()->json(['success' => false, 'message' => 'Signature mismatch'], 403);
        }

        // Ensure Admin role exists
        $role = Role::firstOrCreate(['name' => 'Admin']);

        /** @var \App\Models\User $user */
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                // Random password; not used for SSO
                'password' => bin2hex(random_bytes(8)),
                'email_verified_at' => now(),
            ]
        );
        $user->role()->associate($role);
        $user->save();

        Auth::login($user);

        Log::info('Admin SSO established', ['email' => $email]);

        $redirect = $request->input('redirect', $request->query('redirect')) ?? '/admin';
        if ($redirect) {
            return redirect($redirect);
        }

        // Fallback: tiny HTML page to support iframe usage; sets session cookie
        return response('<!doctype html><html><head><meta charset="utf-8"></head><body>OK</body></html>')
            ->header('Content-Type', 'text/html');
    }
}
