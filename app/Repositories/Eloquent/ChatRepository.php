<?php

namespace App\Repositories\Eloquent;

use App\Data\DynamicFilterData;
use App\Events\ConversationRead;
use App\Events\MessagesDeleted;
use App\Events\MessageSent;
use App\Events\MessagesRead;
use App\Events\TopicClosed;
use App\Events\TopicCreated;
use App\Mail\AgentContactMail;
use App\Models\Chat\Conversation;
use App\Models\Chat\Message;
use App\Models\Chat\Topic;
use App\Models\Property\Property;
use App\Models\User;
use App\Repositories\Contracts\ChatRepositoryInterface;
use App\Services\AutoFIlterAndSortService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use   Illuminate\Support\Str;

class ChatRepository  implements ChatRepositoryInterface
{

    public function __construct(
        private readonly   Message $model,
        private readonly   Conversation $modelConversation,
        private readonly Topic $modelTopic

    ) {}

    public function getList(DynamicFilterData $dynamicFilterData): array
    {
        return (new AutoFIlterAndSortService($this->model))->dynamicFilter($dynamicFilterData);
    }

    public function getMessages(DynamicFilterData $dynamicFilterData)
    {
        return (new AutoFIlterAndSortService($this->model))->dynamicFilter($dynamicFilterData);
        // return $conversation->messages()
        //     ->with('user')
        //     ->latest() // الأحدث أولاً
        //     ->paginate(50); // استخدام paginate للأداء
    }

    public function sendMessageInTopic(Topic $topic, User $user, array $data): Message
    {
        if ($topic->is_closed) {
            throw new Exception("This topic is closed and cannot receive new messages.");
        }

        $message = DB::transaction(function () use ($topic, $user, $data) {
            $message = $topic->messages()->create([
                'user_id' => $user->id,
                'conversation_id' => $topic->conversation_id, // التأكد من ربطها بالمحادثة الأم
                'message' => $data['message'],
            ]);

            // تحديث آخر رسالة في الموضوع والمحادثة الأم
            $topic->update(['last_message_id' => $message->id]);
            $topic->conversation()->update(['last_message_id' => $message?->id]);

            $topic->conversation->touch(); // تحديث updated_at للمحادثة لترتيبها

            return $message;
        });

        // تحميل العلاقات اللازمة قبل البث
        $message->load('user', 'topic');

        // تحويل الرسالة إلى مصفوفة للبث
        $messageArray = $message->toArray();
        $messageArray['conversation_id'] = $topic->conversation_id; // التأكد من وجودها

        // بث الحدث على قناة الموضوع وقناة المحادثة
        // سيساعد هذا في تحديث واجهة المستخدم في الوقت الفعلي
        broadcast(new MessageSent($messageArray))->toOthers();

        return $message;
    }

    public function getMessagesForTopic(DynamicFilterData $dynamicFilterData): array
    {
        // تم تغيير النموذج المستهدف إلى Message model
        return (new AutoFIlterAndSortService($this->model))->dynamicFilter($dynamicFilterData);
    }

    public function markTopicAsRead(Topic $topic, User $user): void
    {
        // الطريقة الأكثر فعالية هي تحديث وقت القراءة في جدول الربط
        // للمحادثة الأم. هذا يضمن أن جميع المواضيع الأقدم تعتبر مقروءة.
        // يمكنك أيضًا إنشاء جدول ربط منفصل للمواضيع إذا احتجت دقة أكبر.
        $topic->conversation->participants()
            ->where('user_id', $user->id)
            ->update(['read_at' => now()]);

        broadcast(new MessagesRead($topic, $user))->toOthers();

        // يمكن بث حدث مخصص لتحديث عدد الرسائل غير المقروءة في الواجهة
        // broadcast(new TopicRead($topic, $user))->toOthers();
    }

    /**
     * تحديد محادثة كمقروءة للمستخدم الحالي.
     */
    public function markAsRead(Conversation $conversation, User $user): void
    {
        $conversation->participants()
            ->where('user_id', $user->id)
            ->update(['read_at' => now()]);

        broadcast(new ConversationRead($conversation, $user))->toOthers();
    }

    public function getTopicsForConversation($conversationId)
    {
        // إضافة حساب الرسائل غير المقروءة لكل موضوع
        /** @var User $user */
        $user = Auth::user();

        $participantData = DB::table('conversation_participants')
            ->where('conversation_id', $conversationId)
            ->where('user_id', $user->id)
            ->first();

        $lastReadAt = $participantData ? $participantData->read_at : null;

        // $lastReadAt = $user->conversations()->find($conversationId)?->pivot?->read_at;


        return Topic::where('conversation_id', $conversationId)
            ->with('lastMessage.user')
            ->withCount(['messages as unread_messages_count' => function ($query) use ($user, $lastReadAt) {
                $query->where('user_id', '!=', $user->id);
                if ($lastReadAt) {
                    $query->where('created_at', '>', $lastReadAt);
                }
            }])
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function createTopic($conversationId, $title)
    {

        $conversation = Conversation::findOrFail($conversationId);
        $topic = $conversation->topics()->create([
            'title' => $title,
        ]);

        // Broadcast the event to other users
        broadcast(new TopicCreated($topic))->toOthers();

        return $topic;
    }

    public function closeTopic($topicId): Topic
    {
        $topic = Topic::with('conversation')->findOrFail($topicId);
        $topic->update(['is_closed' => true]);
        // Optionally broadcast an event
        // event(new TopicClosed($topic));
        return $topic;
    }

    public function reopenTopic($topicId): Topic
    {
        $topic = Topic::with('conversation')->findOrFail($topicId);
        $topic->update(['is_closed' => false]);
        // Optionally broadcast an event
        // event(new TopicReopened($topic));
        return $topic;
    }

    public function bulkDeleteMessages(array $messageIds, int $userId): void
    {
        // Ensure the user can only delete their own messages

        // $firstMessage = Message::find($messageIds[0]);

        // if (!$firstMessage) {
        //     return;
        // }
        // $topicId = $firstMessage->topic_id;
        // $deletedCount = Message::where('user_id', $userId)
        //     ->whereIn('id', $messageIds)
        //     ->delete();

        // if ($deletedCount > 0) {
        //     broadcast(new MessagesDeleted($messageIds, $topicId, $userId))->toOthers();
        // }

        DB::transaction(function () use ($messageIds, $userId) {

            // جلب الرسائل التي سيتم حذفها للوصول إلى معلوماتها
            $messagesToDelete = Message::where('user_id', $userId)
                ->whereIn('id', $messageIds)
                ->get();

            if ($messagesToDelete->isEmpty()) {
                return; // لا يوجد شيء لفعله
            }

            // تحديد المواضيع والمحادثات المتأثرة (لتجنب تحديث كل شيء)
            $affectedTopicIds = $messagesToDelete->pluck('topic_id')->unique()->filter();
            $affectedConversationIds = $messagesToDelete->pluck('conversation_id')->unique()->filter();

            // 1. حذف الرسائل
            $deletedCount = $messagesToDelete->count();
            Message::whereIn('id', $messagesToDelete->pluck('id'))->delete();

            // 2. تحديث last_message_id للمواضيع المتأثرة
            foreach ($affectedTopicIds as $topicId) {
                // ابحث عن آخر رسالة متبقية في هذا الموضوع
                $newLastMessage = Message::where('topic_id', $topicId)->latest()->first();

                // قم بتحديث الموضوع بمعرف الرسالة الجديدة، أو null إذا لم يتبقَ رسائل
                Topic::where('id', $topicId)->update(['last_message_id' => $newLastMessage?->id]);
            }

            // 3. تحديث last_message_id للمحادثات المتأثرة
            foreach ($affectedConversationIds as $conversationId) {
                // ابحث عن آخر رسالة متبقية في هذه المحادثة (عبر كل مواضيعها)
                $newLastMessage = Message::where('conversation_id', $conversationId)->latest()->first();

                // قم بتحديث المحادثة
                Conversation::where('id', $conversationId)->update(['last_message_id' => $newLastMessage?->id]);
            }

            // 4. بث الحدث للعملاء الآخرين
            // بما أن الحذف قد يؤثر على مواضيع مختلفة، يجب أن نرسل حدثًا لكل موضوع
            foreach ($affectedTopicIds as $topicId) {
                broadcast(new MessagesDeleted($messagesToDelete->where('topic_id', $topicId)->pluck('id')->all(), $topicId, $userId))->toOthers();
            }
        });
    }

    public function searchConversations($userId, $query)
    {
        return Conversation::whereHas('participants', fn($q) => $q->where('user_id', $userId))
            ->where(function ($q) use ($query) {
                $q->whereHas('property', fn($q) => $q->where('title', 'like', "%$query%"))
                    ->orWhereHas('topics.messages', fn($q) => $q->where('message', 'like', "%$query%"))
                    ->orWhereHas('participants.user', fn($q) => $q->where('first_name', 'like', "%$query%"));
            })
            ->with(['topics' => function ($q) {
                $q->with('lastMessage');
            }, 'participants.user'])
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
    }

    public function getUserConversations(DynamicFilterData $dynamicFilterData): array
    {
        return (new AutoFIlterAndSortService($this->modelConversation))->dynamicFilter($dynamicFilterData);
    }

    public function handlePropertyContact(Property $property, array $contactData): Message
    {
        $recipient = $property->owner;
        $sender = Auth::user();

        // if (!$sender) {
        //     $sender = User::firstOrCreate(
        //         ['email' => $contactData['email']],
        //         [
        //             'first_name' => $contactData['name'], // التأكد من الحقل الصحيح
        //             'password' => Hash::make(Str::random(10)),
        //         ]
        //     );
        // }

        $conversation = $this->findOrCreateConversation($sender, $recipient);
        $topic = $this->findOrCreateTopicForProperty($conversation, $property);

        // استخدام الدالة الجديدة لإرسال الرسائل
        return $this->sendMessageInTopic($topic, $sender, [
            'message' => $contactData['message']
        ]);
    }

    protected function resolveTopic(Conversation $conversation, Property $property, array $contactData): Topic
    {
        // الحالة 1: إذا تم تحديد معرف الموضوع في البيانات
        if (isset($contactData['topic_id'])) {
            $topic = Topic::find($contactData['topic_id']);

            // التحقق أن الموضوع ينتمي للمحادثة الصحيحة
            if ($topic && $topic->conversation_id === $conversation->id) {
                return $topic;
            }
        }

        // الحالة 2: البحث عن موضوع مرتبط بالعقار
        $propertyTopic = $conversation->topics()
            ->where('property_id', $property->id)
            ->first();

        if ($propertyTopic) {
            return $propertyTopic;
        }

        // الحالة 3: إنشاء موضوع جديد مرتبط بالعقار
        return Topic::create([
            'conversation_id' => $conversation->id,
            'property_id' => $property->id,
            'title' => $property->title,
            'is_closed' => false
        ]);
    }

    protected function findOrCreateDefaultTopic(Conversation $conversation): Topic
    {
        // ابحث عن الموضوع الافتراضي (العام)
        $defaultTopic = $conversation->topics()
            ->where('title', 'عام')
            ->first();

        if (!$defaultTopic) {
            $defaultTopic = Topic::create([
                'conversation_id' => $conversation->id,
                'title' => 'عام',
                'is_closed' => false
            ]);
        }

        return $defaultTopic;
    }

    protected function findOrCreateTopicForProperty(Conversation $conversation, Property $property): Topic
    {
        $topic = $conversation->topics()
            ->where('property_id', $property->id)
            ->first();

        if ($topic) {
            return $topic;
        }
        $topic = Topic::create([
            'conversation_id' => $conversation->id,
            'property_id' => $property->id,
            'title' => $property->title ?? $property->location, // استخدام عنوان العقار كعنوان للموضوع
            'is_closed' => false
        ]);
        info('TopicCreated...');
        broadcast(new TopicCreated($topic))->toOthers();
        return $topic;
    }

    protected function findOrCreateConversation(User $sender, User $recipient): Conversation
    {
        $conversation = Conversation::whereHas('participants', fn($q) => $q->where('user_id', $sender->id))
            ->whereHas('participants', fn($q) => $q->where('user_id', $recipient->id))
            ->first();

        if ($conversation) {
            return $conversation;
        }

        return DB::transaction(function () use ($sender, $recipient) {
            $newConversation = Conversation::create();
            $newConversation->participants()->attach([$sender->id, $recipient->id]);
            return $newConversation;
        });
    }


    /**
     * @deprecated use sendMessageInTopic() instead.
     */
    public function sendMessage(Conversation $conversation, User $user, array $data): Message
    {
        $topicId = $data['topic_id'];
        $topic = Topic::findOrFail($topicId);

        return $this->sendMessageInTopic($topic, $user, $data);
    }

    // public function sendMessage(Conversation $conversation, User $user, array $data): Message
    // {
    //     $message = DB::transaction(function () use ($conversation, $user, $data) {
    //         $topicId = $data['topic_id'] ?? null;

    //         // تحقق أن الموضوع ينتمي للمحادثة
    //         if ($topicId) {
    //             $topic = Topic::find($topicId);
    //             if (!$topic || $topic->conversation_id !== $conversation->id) {
    //                 throw new \Exception('Topic does not belong to this conversation');
    //             }
    //         }

    //         $message = $conversation->messages()->create([
    //             'user_id' => $user->id,
    //             'topic_id' => $topicId,
    //             'message' => $data['message'],
    //             'name' => $data['name'] ?? null,
    //             'email' => $data['email'] ?? null,
    //             'mobile' => $data['mobile'] ?? null
    //         ]);

    //         // تحديث آخر رسالة في الموضوع
    //         if ($topicId) {
    //             Topic::find($topicId)->update(['last_message_id' => $message->id]);
    //         }


    //         $conversation->update([
    //             'last_message_id' => $message->id,
    //             'updated_at' => now()
    //         ]);

    //         return $message;
    //     });

    //     $message->load('user', 'topic');
    //     broadcast(new MessageSent($message->toArray()))->toOthers();

    //     return $message;
    // }
}
