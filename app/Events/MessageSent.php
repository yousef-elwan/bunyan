<?php

namespace App\Events;

use App\Models\Chat\Message;
use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $message;
    public function __construct(array $message)
    {
        $this->message = $message;
        // تأكد من وجود conversation_id في المصفوفة
        $this->message['conversation_id'] = $message['topic']['conversation_id'] ?? $message['conversation_id'];
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('topic.' . $this->message['topic_id']),
            new PrivateChannel('conversation.' . $this->message['conversation_id']),
        ];
    }

    // يمكنك تخصيص اسم الحدث إذا أردت
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    // public function broadcastWith(): array
    // {
    //     return $this->message;
    // }
    public function broadcastWith()
    {
        $message =  $this->message;
        return [
            'id' => $message['id'],
            'conversation_id' => $message['conversation_id'],
            'topic_id' => $message['topic_id'],
            'property_id' => $message['topic']['property_id'] ?? null,
            'user_id' => $message['user_id'],
            'message' => $message['message'],
            'created_at' => (new Carbon($message['created_at']))->toDateTimeString(),
            'user' => [
                'id' => $message['user']['id'],
                'name' => $message['user']['name'],
                'image_url' => $message['user']['image_url']
            ],
            'topic' => $message['topic'] ? [
                'id' => $message['topic']['id'],
                'title' => $message['topic']['title'],
                'property_id' => $message['topic']['property_id']
            ] : null
        ];
    }
}
