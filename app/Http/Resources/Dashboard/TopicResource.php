<?php

namespace App\Http\Resources\Dashboard;

use App\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class TopicResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function resolveData(Request $request): array
    {
        $resource =  $this->resource;


        $data = [
            'id' => $resource['id'],
            'title' => $resource['title'],
            'is_closed' => $resource['is_closed'],
        ];


        if (array_key_exists('unread_messages_count', $resource)) {
            $data['unread_messages_count'] = (int) $resource['unread_messages_count'];
        } else {
            // إضافة قيمة افتراضية إذا لم يكن الحقل موجودًا
            $data['unread_messages_count'] = 0;
        }

        // if (array_key_exists('last_message', $resource)) {
        //     $data['last_message'] = $resource['last_message'] ? [
        //         'id' => $resource['last_message']['id'],
        //         'message' => $resource['last_message']['message'],
        //         'created_at' => $resource['last_message']['created_at'],
        //         'user' => [
        //             'id' => $resource['last_message']['user']['id'],
        //             'name' => $resource['last_message']['user']['name']
        //         ]
        //     ] : null;
        // }

        if (array_key_exists('last_message', $resource) && $resource['last_message']) {
            $data['last_message'] = [
                'id' => $resource['last_message']['id'],
                'message' => $resource['last_message']['message'],
                'created_at' => $resource['last_message']['created_at'],
                'user' => [
                    'id' => $resource['last_message']['user']['id'],
                    'name' => $resource['last_message']['user']['name'] ?? 'Unknown User', // قيمة افتراضية
                    // يمكنك إضافة المزيد من تفاصيل المستخدم هنا إذا لزم الأمر
                ]
            ];
        } else {
            // إضافة قيمة `null` إذا لم تكن هناك رسالة أخيرة
            $data['last_message'] = null;
        }
        return $data;
    }
}
