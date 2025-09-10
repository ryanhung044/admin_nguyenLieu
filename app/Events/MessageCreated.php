<?php

// namespace App\Events;

// use App\Models\Message;
// use Illuminate\Broadcasting\Channel;
// use Illuminate\Broadcasting\InteractsWithSockets;
// use Illuminate\Broadcasting\PresenceChannel;
// use Illuminate\Broadcasting\PrivateChannel;
// use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
// use Illuminate\Foundation\Events\Dispatchable;
// use Illuminate\Queue\SerializesModels;

// class MessageCreated implements ShouldBroadcast

// {
//     use Dispatchable, InteractsWithSockets, SerializesModels;
//     public $message;
//     public $conversationId;

//     public function __construct(Message $message)
//     {
//         $this->message = $message->load('conversation.user');
//         $this->conversationId = $message->conversation_id;
//     }

//     public function broadcastAs()
//     {
//         return 'MessageCreated';
//     }


//     public function broadcastOn(): array
//     {
//         return [
//             new PrivateChannel("conversation.{$this->conversationId}"),
//         ];
//     }
// }


namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $conversationId;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        // load quan hệ cần thiết để khi broadcast gửi ra có đủ data
        $this->message = $message->load('conversation.user');
        $this->conversationId = $message->conversation_id;
    }

    /**
     * Tên sự kiện broadcast ra client
     */
    public function broadcastAs()
    {
        return 'MessageCreated';
    }

    /**
     * Kênh mà event sẽ phát ra
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("conversation.{$this->conversationId}"),
        ];
    }

    /**
     * Payload gửi ra ngoài
     */
    public function broadcastWith(): array
    {
        return [
            'id'            => $this->message->id,
            'conversation'  => $this->conversationId,
            'sender_type'   => $this->message->sender_type,
            'message_type'  => $this->message->message_type,
            'message_text'  => $this->message->message_text,
            'sent_at'       => $this->message->sent_at,
            'user'          => $this->message->conversation->user,
        ];
    }
}

