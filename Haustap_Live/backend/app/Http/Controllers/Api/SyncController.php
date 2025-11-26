<?php
namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class SyncController
{
    private function getAccessToken(): ?string
    {
        try {
            if (class_exists('Google\\Auth\\ApplicationDefaultCredentials')) {
                $scopes = ['https://www.googleapis.com/auth/datastore'];
                $creds = \Google\Auth\ApplicationDefaultCredentials::getCredentials($scopes);
                $tokenInfo = $creds->fetchAuthToken();
                return is_array($tokenInfo) ? ($tokenInfo['access_token'] ?? null) : null;
            }
        } catch (\Throwable $e) {
        }
        return null;
    }

    private function httpJson(string $url, array $opts): array
    {
        $ctx = stream_context_create(['http' => $opts]);
        $raw = @file_get_contents($url, false, $ctx);
        return json_decode($raw ?: 'null', true) ?: [];
    }

    public function status(): JsonResponse
    {
        $users = (int)DB::table('users')->count();
        $providers = (int)DB::table('providers')->count();
        $bookings = (int)DB::table('bookings')->count();
        $projectId = env('FIREBASE_PROJECT_ID');
        $firestore = false;
        if ($projectId && ($token = $this->getAccessToken())) {
            $url = 'https://firestore.googleapis.com/v1/projects/' . $projectId . '/databases/(default)/documents:runQuery';
            $body = json_encode(['structuredQuery' => ['from' => [['collectionId' => 'users']], 'limit' => 1]]);
            $j = $this->httpJson($url, ['method' => 'POST','header' => ['Authorization: Bearer ' . $token, 'Content-Type: application/json','Accept: application/json'],'content' => $body,'ignore_errors' => true,'timeout' => 10]);
            $firestore = !empty($j);
        }
        return response()->json(['users' => $users, 'providers' => $providers, 'bookings' => $bookings, 'firestore' => $firestore]);
    }

    public function syncUsersToFirebase(): JsonResponse
    {
        $projectId = env('FIREBASE_PROJECT_ID');
        $token = $this->getAccessToken();
        if (!$projectId || !$token) return response()->json(['success' => false, 'message' => 'adc_unavailable'], 500);
        $base = 'https://firestore.googleapis.com/v1/projects/' . $projectId . '/databases/(default)/documents/users';
        $users = DB::table('users')->select('id','name','email','role','status')->get();
        foreach ($users as $u) {
            $doc = [
                'fields' => [
                    'name' => ['stringValue' => (string)$u->name],
                    'email' => ['stringValue' => (string)$u->email],
                    'role' => ['stringValue' => (string)($u->role ?? '')],
                    'status' => ['stringValue' => (string)($u->status ?? '')],
                ]
            ];
            $url = $base . '/' . urlencode((string)$u->id) . '?mask.fieldPaths=name&mask.fieldPaths=email&mask.fieldPaths=role&mask.fieldPaths=status';
            $this->httpJson($url, ['method' => 'PATCH','header' => ['Authorization: Bearer ' . $token, 'Content-Type: application/json','Accept: application/json'],'content' => json_encode($doc),'ignore_errors' => true,'timeout' => 10]);
        }
        return response()->json(['success' => true, 'count' => count($users)]);
    }

    public function syncUsersFromFirebase(): JsonResponse
    {
        $projectId = env('FIREBASE_PROJECT_ID');
        $token = $this->getAccessToken();
        if (!$projectId || !$token) return response()->json(['success' => false, 'message' => 'adc_unavailable'], 500);
        $url = 'https://firestore.googleapis.com/v1/projects/' . $projectId . '/databases/(default)/documents/users?pageSize=200';
        $j = $this->httpJson($url, ['method' => 'GET','header' => ['Authorization: Bearer ' . $token, 'Accept: application/json'],'ignore_errors' => true,'timeout' => 10]);
        $docs = is_array($j['documents'] ?? null) ? $j['documents'] : [];
        $upserted = 0;
        foreach ($docs as $d) {
            $f = $d['fields'] ?? [];
            $name = (string)($f['name']['stringValue'] ?? '');
            $email = (string)($f['email']['stringValue'] ?? '');
            $role = (string)($f['role']['stringValue'] ?? '');
            $status = (string)($f['status']['stringValue'] ?? '');
            if ($email === '') continue;
            $exists = DB::table('users')->where('email', $email)->exists();
            if ($exists) {
                DB::table('users')->where('email', $email)->update(['name' => $name ?: $email, 'role' => $role, 'status' => $status, 'updated_at' => now()]);
            } else {
                DB::table('users')->insert(['name' => $name ?: $email, 'email' => $email, 'role' => $role ?: 'client', 'status' => $status, 'password' => '', 'created_at' => now(), 'updated_at' => now()]);
            }
            $upserted++;
        }
        return response()->json(['success' => true, 'count' => $upserted]);
    }

    public function syncBookings(): JsonResponse
    {
        $projectId = env('FIREBASE_PROJECT_ID');
        $token = $this->getAccessToken();
        if (!$projectId || !$token) return response()->json(['success' => false, 'message' => 'adc_unavailable'], 500);
        $base = 'https://firestore.googleapis.com/v1/projects/' . $projectId . '/databases/(default)/documents/bookings';
        $rows = DB::table('bookings')->select('id','client_id','provider_id','service_name','scheduled_date','scheduled_time','price','status','rating')->get();
        foreach ($rows as $r) {
            $doc = [
                'fields' => [
                    'client_id' => ['integerValue' => (string)((int)$r->client_id)],
                    'provider_id' => ['integerValue' => (string)((int)$r->provider_id)],
                    'service_name' => ['stringValue' => (string)$r->service_name],
                    'scheduled_date' => ['stringValue' => (string)$r->scheduled_date],
                    'scheduled_time' => ['stringValue' => (string)$r->scheduled_time],
                    'price' => ['doubleValue' => (double)$r->price],
                    'status' => ['stringValue' => (string)$r->status],
                    'rating' => ['doubleValue' => (double)($r->rating ?? 0)],
                ]
            ];
            $url = $base . '/' . urlencode((string)$r->id);
            $this->httpJson($url, ['method' => 'PATCH','header' => ['Authorization: Bearer ' . $token, 'Content-Type: application/json','Accept: application/json'],'content' => json_encode($doc),'ignore_errors' => true,'timeout' => 10]);
        }
        return response()->json(['success' => true, 'count' => count($rows)]);
    }

    public function fullSync(): JsonResponse
    {
        $a = $this->syncUsersToFirebase()->getData(true);
        $b = $this->syncBookings()->getData(true);
        return response()->json(['success' => ($a['success'] ?? false) && ($b['success'] ?? false)]);
    }
}