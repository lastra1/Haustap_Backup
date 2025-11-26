<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class ChatController
{
    public function open(Request $request): JsonResponse
    {
        $bookingId = (int)$request->input('booking_id');
        return response()->json(['success' => true, 'room_id' => $bookingId]);
    }

    public function listMessages(int $booking_id): JsonResponse
    {
        $rows = DB::table('chat_messages')->where('room_id', $booking_id)->orderBy('id')->get()->toArray();
        return response()->json(['success' => true, 'messages' => $rows]);
    }

    public function sendMessage(Request $request, int $booking_id): JsonResponse
    {
        $senderId = (int)$request->input('sender_id');
        $message = trim((string)$request->input('message'));
        if ($message === '') return response()->json(['success' => false], 422);
        DB::table('chat_messages')->insert(['room_id' => $booking_id, 'sender_id' => $senderId, 'message' => $message, 'ts' => now()]);
        return response()->json(['success' => true]);
    }
}