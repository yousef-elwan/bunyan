<?php

use App\Models\Chat\Conversation;
use App\Models\Chat\Topic;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Broadcast::channel('conversation.{conversation}', function (User $user, Conversation $conversation) {

//     // -- للتصحيح (يمكنك إزالتها لاحقًا) --
//     // هذا سيسجل معلومات مفيدة في ملف storage/logs/laravel.log
//     Log::info("Authorizing user [{$user->id}] for conversation [{$conversation->id}]");

//     // التحقق الأساسي: هل المستخدم الحالي هو أحد المشاركين في هذه المحادثة؟
//     $isParticipant = $conversation->participants->contains($user);

//     // -- للتصحيح (يمكنك إزالتها لاحقًا) --
//     Log::info("User is participant: " . ($isParticipant ? 'Yes' : 'No'));

//     // إذا كان المستخدم مشاركًا، أرجع `true` للسماح بالاشتراك.
//     // إذا لم يكن، سيتم إرجاع `false` ضمنيًا، مما يسبب خطأ 403.
//     return $isParticipant;

//     // الطريقة المختصرة (تؤدي نفس الغرض)
//     // return $conversation->participants->contains($user);
// });

Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    // تحقق مما إذا كان المستخدم مشاركًا في المحادثة
    return $user->conversations()->where('conversation_id', $conversationId)->exists();
});

Broadcast::channel('topic.{topicId}', function ($user, $topicId) {
    $topic = Topic::find($topicId);
    if (!$topic) {
        return false;
    }
    // تحقق مما إذا كان المستخدم مشاركًا في المحادثة الأم للموضوع
    return $topic->conversation->participants()->where('user_id', $user->id)->exists();
});
