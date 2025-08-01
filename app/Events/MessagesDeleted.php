<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessagesDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The IDs of the deleted messages.
     *
     * @var array
     */
    public $message_ids;

    /**
     * The topic ID from which messages were deleted.
     *
     * @var int
     */
    public $topic_id;

    /**
     * The user ID of the person who deleted the messages.
     *
     * @var int
     */
    public $deleter_id;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array $messageIds, int $topicId, int $deleterId)
    {
        $this->message_ids = $messageIds;
        $this->topic_id = $topicId;
        $this->deleter_id = $deleterId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // We broadcast on the topic channel so anyone in it gets the update.
        return new PrivateChannel('topic.' . $this->topic_id);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'messages.deleted';
    }
}
