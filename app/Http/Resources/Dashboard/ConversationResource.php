<?php

namespace App\Http\Resources\Dashboard;

use App\Http\Resources\BaseJsonResource;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ConversationResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function resolveData(Request $request): array
    {
        $resource = $this->resource;
        return [
            'id' => $resource['id'],
            'name' => $resource['participants'][0]->name ?? __('dashboard/chat.general'),
            'member_count' => collect($resource['participants'])->count(),
            'topics_count' => $resource['topics_count'],
            'last_message' => [
                'id' => $resource['last_message']['id'],
                'message' => $resource['last_message']['message'] ?? __('dashboard/chat.no_messages'),
                'created_at' => $resource['last_message']['created_at'],
                'topic' => $resource['last_message']['topic'],
                'user' => [
                    'id' => $resource['last_message']['user']['id'],
                    'name' => $resource['last_message']['user']['name']
                ]
            ],
            'unread_messages_count' => $resource['unread_messages_count']
        ];
    }
}
