<?php

namespace App\Http\Resources\Dashboard;

use App\Http\Resources\BaseJsonResource;
use App\Http\Resources\Web\PropertyResource;

class ReportResource extends BaseJsonResource
{

    public function resolveData($request)
    {

        $data = [
            'id' => $this->resource['id'] ?? null,

            'property_title' => optional($this->resource['property']['translation'] ?? null)['name']
                ?? "عقار رقم #{$this->resource['property_id']}",

            'property_edit_url' => isset($this->resource['property_id'])
                ? route('dashboard.properties.edit', $this->resource['property_id'])
                : null,

            'reporter_name' => isset($this->resource['reporter'])
                ? $this->resource['reporter']['first_name'] . ' ' . $this->resource['reporter']['last_name']
                : null,

            'email' => $this->resource['reporter']['email'] ?? $this->resource['email'] ?? null,
            'mobile' => $this->resource['reporter']['mobile'] ?? $this->resource['mobile'] ?? null,

            'message' => $this->resource['message'] ?? null,
            'report_status_id' => $this->resource['report_status_id'] ?? null,

            'type' => $this->whenLoaded('type'),

            'status' => $this->whenLoaded('status'),

            'reporter' => $this->whenLoaded('reporter'),

            'property' => $this->whenLoaded('property', fn() => PropertyResource::make($this->property)->toArray($request)),
            'type' => $this->whenLoaded('type', fn() => $this->type),

            'created_at' => isset($this->resource['created_at'])
                ? \Carbon\Carbon::parse($this->resource['created_at'])->format('Y-m-d H:i')
                : null,
        ];

        return $data;
    }
}
