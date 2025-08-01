<?php

namespace App\Http\Resources\Dashboard;

use App\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;
use App\Http\Resources\Dashboard\TopicResource;
use Carbon\Carbon;

class MessageResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function resolveData(Request $request): array
    {

        $resource = $this->resource;

        $isRead = false;

        $created_at = (new Carbon($resource['created_at']));
        $data = [
            // الحقول الأساسية من جدول الرسائل
            'id' => $resource['id'],
            'message' => $resource['message'],
            'created_at' => $created_at->toIso8601String(),
            'updated_at' => (new Carbon($resource['updated_at']))->toIso8601String(),

            // تضمين العلاقات (إذا تم تحميلها)
            // استخدام whenLoaded يضمن عدم حدوث أخطاء إذا لم تكن العلاقة محملة
            'user' => $resource['user'],
            'topic' => new TopicResource($resource['topic']),

            // يمكنك أيضًا تضمين الحقول التي تم تحميلها من العلاقات مباشرة
            'user_id' => $resource['user_id'],
            'topic_id' => $resource['topic_id'],
            'conversation_id' => $resource['conversation_id'],

            // يمكنك إضافة بيانات مخصصة لا توجد في قاعدة البيانات
            'is_sender' => auth()->check() ? $resource['user_id'] === auth()->id() : false,
        ];

        if (array_key_exists('participants', $resource)) {
            $otherParticipant = collect($resource['participants'])->firstWhere('id', '!=', auth()->id());
            if ($otherParticipant && $otherParticipant->pivot->read_at && $created_at->lessThanOrEqualTo($otherParticipant->pivot->read_at)) {
                $isRead = true;
            }
        }

        $data['status'] = $isRead ? 'read' : 'sent';
        return $data;
    }
}
