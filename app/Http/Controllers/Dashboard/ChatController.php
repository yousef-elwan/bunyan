<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\StoreMessageRequest;
use App\Http\Resources\Dashboard\ConversationResource;
use App\Http\Resources\Dashboard\MessageResource;
use App\Http\Resources\Dashboard\TopicResource;
use App\Models\Chat\Topic;
use App\Models\User;
use App\Repositories\Contracts\ChatRepositoryInterface;
use App\Services\AutoFIlterAndSortService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ChatController extends Controller
{

    public function __construct(
        protected ChatRepositoryInterface $repo
    ) {}

    public function index(): View
    {
        /** @var User $user  auth user */
        $user = Auth::user();
        // $conversations = $this->repo->getUserConversations($user);
        return view('dashboard.pages.chatting.list', compact('user'));
    }

    public function fetchConversations(Request $request): JsonResponse
    {
        $user = Auth::user();

        // $conversations = AutoFIlterAndSortService::dynamicSearchFromRequest(
        //     getFunction: [$this->repo, 'getUserConversations'],
        //     extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) use ($user) {
        //         $query->whereHas('participants', function ($query) use ($user) {
        //             $query->where('user_id', $user->id);
        //         })
        //             ->with([
        //                 'participants' => function ($query) use ($user) {
        //                     $query->where('user_id', '!=', $user->id);
        //                 },
        //                 'topics' => function ($query) {
        //                     $query->with('lastMessage.user')->orderBy('updated_at', 'desc');
        //                 },
        //                 'lastMessage.user',
        //             ])
        //             ->addSelect(['unread_messages_count' => function ($query) use ($user) {
        //                 $query->selectRaw('count(*)')
        //                     ->from('messages')
        //                     ->whereColumn('messages.conversation_id', 'conversations.id')
        //                     ->where('messages.user_id', '!=', $user->id)
        //                     ->where(function ($subQuery) use ($user) {
        //                         $subQuery->whereRaw('messages.created_at > (SELECT read_at FROM conversation_participants WHERE conversation_id = conversations.id AND user_id = ?)', [$user->id])
        //                             ->orWhereRaw('(SELECT read_at FROM conversation_participants WHERE conversation_id = conversations.id AND user_id = ?) IS NULL', [$user->id]);
        //                     });
        //             }])
        //             ->orderBy('updated_at', 'desc');
        //     },
        // );

        // $conversations = AutoFIlterAndSortService::dynamicSearchFromRequest(
        //     getFunction: [$this->repo, 'getUserConversations'],
        //     extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) use ($user) {
        //         $query->whereHas('participants', function ($query) use ($user) {
        //             $query->where('user_id', $user->id);
        //         })
        //             ->with([
        //                 'participants' => function ($query) use ($user) {
        //                     $query->where('user_id', '!=', $user->id);
        //                         // ->with('user');
        //                 },
        //                 'topics' => function ($query) {
        //                     $query->with('lastMessage.user')
        //                         ->orderBy('updated_at', 'desc');
        //                 },
        //                 'lastMessage.topics' // العلاقة الجديدة
        //             ])
        //             ->addSelect(['unread_messages_count' => function ($query) use ($user) {
        //                 $query->selectRaw('count(*)')
        //                     ->from('messages')
        //                     ->whereColumn('messages.conversation_id', 'conversations.id')
        //                     ->where('messages.user_id', '!=', $user->id)
        //                     ->where(function ($subQuery) use ($user) {
        //                         $subQuery->whereRaw('messages.created_at > (SELECT read_at FROM conversation_participants WHERE conversation_id = conversations.id AND user_id = ?)', [$user->id])
        //                             ->orWhereRaw('(SELECT read_at FROM conversation_participants WHERE conversation_id = conversations.id AND user_id = ?) IS NULL', [$user->id]);
        //                     });
        //             }])
        //             ->orderBy('updated_at', 'desc');
        //     },
        // );
        $conversations = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->repo, 'getUserConversations'],
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) use ($user) {
                $query->whereHas('participants', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                    ->with([
                        'participants' => function ($query) use ($user) {
                            $query->where('user_id', '!=', $user->id);
                        },
                        // إزالة تحميل المواضيع هنا
                        'lastMessage.user',
                        'lastMessage.topic',
                    ])
                    ->withCount(['topics as topics_count']) // فقط عدد المواضيع
                    ->addSelect(['unread_messages_count' => function ($query) use ($user) {
                        $query->selectRaw('count(*)')
                            ->from('messages')
                            ->whereColumn('messages.conversation_id', 'conversations.id')
                            ->where('messages.user_id', '!=', $user->id)
                            ->where(function ($subQuery) use ($user) {
                                $subQuery->whereRaw('messages.created_at > (SELECT read_at FROM conversation_participants WHERE conversation_id = conversations.id AND user_id = ?)', [$user->id])
                                    ->orWhereRaw('(SELECT read_at FROM conversation_participants WHERE conversation_id = conversations.id AND user_id = ?) IS NULL', [$user->id]);
                            });
                    }])
                    ->orderBy('updated_at', 'desc');
            },
        );

        // return $conversations;
        // تحويل البيانات إلى الهيكل المطلوب
        $formattedConversations = ConversationResource::collection($conversations['data']);
        return successResponse(
            message: "",
            data: $formattedConversations,
            // data: $conversations,
            pagination: $conversations['pagination']
        );
    }

    public function show(Request $request, Topic $topic): JsonResponse
    {
        // التحقق من أن المستخدم لديه صلاحية الوصول لهذا الموضوع
        // $this->authorize('access', $topic);

        $messages = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->repo, 'getMessagesForTopic'], // استخدم دالة جديدة في الـ repo
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) use ($topic) {
                // الفلترة حسب topic_id بدلاً من conversation_id
                $query->where('topic_id', $topic->id)->with(['user', 'topic'])
                    ->with(['user', 'topic.conversation.participants'])
                    ->latest();
            },
        );

        // هنا يجب تعديل repo->markAsRead ليعمل مع Topic
        $this->repo->markTopicAsRead($topic, $request->user());

        $otherParticipant = $topic->conversation->participants()
            ->where('user_id', '!=', Auth::id())
            ->first();

        // تأكد من أن الـ Resource الخاص بالرسائل يعيد الهيكل الصحيح
        return successResponse(
            message: '',
            data: [
                'messages' => MessageResource::collection($messages['data']),
                'other_participant_read_at' => (new Carbon($otherParticipant?->pivot?->read_at))->toIso8601String()
            ],
            pagination: $messages['pagination']
        );
    }

    public function store(StoreMessageRequest $request, Topic $topic): JsonResponse
    {
        // $this->authorize('access', $topic);

        // يجب تعديل repo->sendMessage ليقبل Topic
        $message = $this->repo->sendMessageInTopic(
            $topic,
            $request->user(),
            $request->validated()
        );

        // تأكد من أن الـ Resource يعيد الهيكل الصحيح
        return successResponse(
            data: new MessageResource($message->toArray()),
            stateCode: 201,
        );
    }

    public function markAsRead(Request $request,  Topic $topic): JsonResponse
    {
        // $this->authorize('access', $topic);
        $this->repo->markTopicAsRead($topic, $request->user());

        return successResponse(
            message: __('')
        );
    }

    public function fetchTopics(Request $request,  string $conversation)
    {

        $topics = $this->repo->getTopicsForConversation($conversation);
        // $topics = $conversation->topics()
        //     ->with('lastMessage.user')
        //     ->orderBy('updated_at', 'desc')
        //     ->get();

        return successResponse(
            data: $topics->map(fn($topic) => TopicResource::make($topic->toArray()))
            // data: TopicResource::collection($topics)
            // data: $topics
        );
    }

    public function createTopic(Request $request, $conversationId)
    {
        $request->validate(['title' => 'required|string|max:255']);

        $topic = $this->repo->createTopic($conversationId, $request->title);
        return successResponse(data: $topic, stateCode: 201);
    }

    public function closeTopic($topicId)
    {
        $this->repo->closeTopic($topicId);
        return successResponse(message: 'Topic closed');
    }

    public function reopenTopic($locale, Topic $topic)
    {
        // You might want to add an authorization policy here
        // $this->authorize('reopen', $topic);
        $this->repo->reopenTopic($topic->id);
        return successResponse(message: 'Topic reopened successfully.');
    }

    public function bulkDeleteMessages(Request $request)
    {
        $request->validate([
            'message_ids' => 'required|array',
            'message_ids.*' => 'exists:messages,id'
        ]);

        // Authorize that the user can delete these messages
        $this->repo->bulkDeleteMessages($request->input('message_ids'), Auth::id());

        return successResponse(message: 'Messages deleted successfully.');
    }


    public function search(Request $request)
    {
        $conversations = $this->repo->searchConversations(
            Auth::id(),
            $request->input('query')
        );

        return successResponse(data: $conversations);
    }
}
