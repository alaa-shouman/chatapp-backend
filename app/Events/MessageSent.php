<?php

namespace App\Events;

use App\Models\messages;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $user;
    public $chatId;

    /**
     * Create a new event instance.
     */
    public function __construct(messages $message, User $user, string $chatId)
    {
        $this->message = $message;
        $this->user = $user;
        $this->chatId = $chatId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        Log::info('MessageSent broadcastOn', ['chatId' => $this->chatId, 'channel' => 'chat.' . $this->chatId]);
        return new Channel('chat.' . $this->chatId);
        // return ['chat'.$this->chatId];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith()
    {
        Log::info('MessageSent broadcastWith', [
            'message_id' => $this->message->id,
            'message_content' => $this->message->message,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'chat_id' => $this->chatId
        ]);

        return [
            'id' => $this->message->id,
            'message' => $this->message->message,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'created_at' => $this->message->created_at->toISOString(),
        ];
    }
}
