<?php

namespace App\Events;

use App\Models\Chat\Topic;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessagesRead implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $topic_id;
    public int $conversation_id;
    public int $reader_id; // من قام بالقراءة

    public function __construct(Topic $topic, User $reader)
    {
        $this->topic_id = $topic->id;
        $this->conversation_id = $topic->conversation_id;
        $this->reader_id = $reader->id;
    }

    public function broadcastOn(): array
    {
        // البث على قناة الموضوع لإعلام المشاركين الآخرين
        return [
            new PrivateChannel('topic.' . $this->topic_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'messages.read';
    }
}
