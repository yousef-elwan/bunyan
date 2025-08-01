<?php

namespace App\Http\Resources\Web;

use App\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class PagesMetaResource extends BaseJsonResource
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
                'name',
                'title',
                'description',
                'keywords',
                'translations',
            ])->all();
        return $data;
    }
}
