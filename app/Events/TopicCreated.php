<?php

namespace App\Events;

use App\Http\Resources\Dashboard\TopicResource;
use App\Models\Chat\Topic;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TopicCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The topic data, already formatted by its resource.
     *
     * @var array
     */
    public $topic;

    public function __construct(Topic $topic)
    {
        // نستخدم TopicResource لضمان أن البيانات المرسلة متناسقة
        $this->topic = (new TopicResource($topic->toArray()))->resolve();
    }

    public function broadcastOn(): array
    {
        // البث على قناة المحادثة الأم، ليسمعها كل المشاركين
        return [
            new PrivateChannel('conversation.' . $this->topic['conversation_id']),
        ];
    }

    public function broadcastAs(): string
    {
        return 'topic.created';
    }
}