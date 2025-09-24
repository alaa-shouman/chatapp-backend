<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\chat_participants;
use App\Models\chats;
use App\Models\messages;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getChatMessages(Request $request, $chatId)
    {
        try {
            // Find chat by chat_id (UUID) but use the id (integer) for relationships
            $chat = chats::where('chat_id', $chatId)->first();
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 20);
            if (!$chat) {
                return response()->json([
                    'chat' => null,
                    'messages' => []
                ]);
            }

            // Use the integer id for the foreign key relationship
            $messages = messages::where('chat_id', $chat->id)
                ->with('user')
                ->orderBy('created_at', 'asc')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'chat' => $chat,
                'messages' => $messages
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'chat' => null,
                'messages' => []
            ]);
        }
    }

    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'chatId' => 'required|string|min:1',
            'message' => 'required|string|min:1',
        ]);

        [$uuid1, $uuid2] = explode('_', $validated['chatId']);

        // Convert UUIDs to integer IDs
        $user1 = User::where('uuid', $uuid1)->first();
        $user2 = User::where('uuid', $uuid2)->first();

        if (!$user1 || !$user2) {
            return response()->json(['message' => 'Invalid users'], 422);
        }

        $sender = Auth::user();
        $sender_id = $sender->id; // integer ID
        $receiver_id = $sender_id === $user1->id ? $user2->id : $user1->id;

        return DB::transaction(function () use ($validated, $sender_id, $receiver_id, $sender) {
            $chat = chats::where('chat_id', $validated['chatId'])->first();

            if (!$chat) {
                $chat = chats::create([
                    'chat_id' => $validated['chatId'], // Store UUID string
                    'type' => 'private',
                ]);

                chat_participants::create([
                    'chat_id' => $chat->id, // Use integer id
                    'user_id' => $sender_id,
                ]);

                chat_participants::create([
                    'chat_id' => $chat->id, // Use integer id
                    'user_id' => $receiver_id,
                ]);
            }

            $message = messages::create([
                'chat_id' => $chat->id, // Use integer id, not UUID string
                'user_id' => $sender_id,
                'message' => $validated['message'],
            ]);

            $message->load('user');

            // Broadcast the message
            broadcast(new MessageSent($message, $sender, $validated['chatId']))->toOthers();
            Log::info($validated['chatId']);
            return response()->json([
                'message' => 'Message sent successfully',
                'data' => $message->load('user')->load('chat'),
            ], 201);
        });
    }
}
