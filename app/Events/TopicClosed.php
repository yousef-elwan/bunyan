<?php

namespace App\Events;

use App\Models\Chat\Topic;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TopicClosed implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $topic_id;
    public $conversation_id;

    public function __construct(Topic $topic)
    {
        $this->topic_id = $topic->id;
        $this->conversation_id = $topic->conversation_id;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('topic.' . $this->topic_id),
            new PrivateChannel('conversation.' . $this->conversation_id)
        ];
    }

    public function broadcastAs(): string
    {
        return 'topic.closed';
    }
}
