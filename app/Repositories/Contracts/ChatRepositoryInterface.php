<?php

namespace App\Repositories\Contracts;

use App\Data\DynamicFilterData;
use App\Models\Chat\Conversation;
use App\Models\Chat\Message;
use App\Models\Chat\Topic;
use App\Models\Property\Property;
use App\Models\User;

interface ChatRepositoryInterface
{
    public function sendMessage(Conversation $conversation, User $user, array $data);
    public function getMessages(DynamicFilterData  $dynamicFilterData);
    public function markAsRead(Conversation $conversation, User $user);
    public function getUserConversations(DynamicFilterData $dynamicFilterData): array;
    public function handlePropertyContact(Property $property, array $contactData): Message;

    public function getTopicsForConversation($conversationId);
    public function createTopic($conversationId, $title);
    public function reopenTopic(int $topicId): Topic;
    public function closeTopic(int $topicId): Topic;
    public function bulkDeleteMessages(array $messageIds, int $userId): void;
    public function searchConversations($userId, $query);
    public function markTopicAsRead(Topic $topic, User $user): void;
    public function sendMessageInTopic(Topic $topic, User $user, array $data): Message;
}
