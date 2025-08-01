<?php

namespace App\Http\Resources\Web;

use App\Http\Resources\BaseJsonResource;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CategoryResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function resolveData(Request $request)
    {

        $data = \Illuminate\Http\Resources\Json\JsonResource::toArray($request);

        $data = collect($data)
            ->only([
                'id',
                'image_url',
                'properties_count',
                'name',
                'properties',
                'created_at',
            ])->all();

        if (array_key_exists('properties', $data)) {
            $data['properties'] = collect($data['properties'])->map(
                fn($property) =>
                PropertyResource::make($property)->toArray(request())
            );
        }

        if (isset($data['created_at'])) {
            $data['created_at'] = Carbon::parse($data['created_at'])->format('Y-m-d');
        }
        return $data;
    }
}
