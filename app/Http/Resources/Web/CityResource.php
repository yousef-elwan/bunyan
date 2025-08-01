<?php

namespace App\Http\Resources\Web;

use App\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class CityResource extends BaseJsonResource
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
                'image_url',
                'properties_count',
            ])->all();
        return $data;
    }
}
