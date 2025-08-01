<?php

namespace App\Http\Resources\Web;

use App\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class UserResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function resolveData(Request $request)
    {
        $data = \Illuminate\Http\Resources\Json\JsonResource::toArray($request);

        $data['is_active_text'] =  $data['is_active'] ? 'Active' : 'Inactive';
        // $data = collect($data)
        //     ->only([
        //         'id',
        //         'name',
        //     ])->all();

        return $data;
    }
}
