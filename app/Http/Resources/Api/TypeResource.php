<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class TypeResource extends BaseJsonResource
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
                'translations',
            ])->all();

        if (array_key_exists('translations', $data)) {
            $data['translations'] = collect($data['translations'])->map(function ($localeData) {
                $localeData = is_array($localeData) ? $localeData : $localeData->toArray();
                $localeData = collect($localeData)
                    ->only([
                        'locale',
                        'name'
                    ])->all();
                return $localeData;
            })->groupBy('locale')->all();

            foreach (array_keys($data['translations']) as $key) {
                $localeData = $data['translations'][$key][0];
                unset($localeData['locale']);
                $data['translations'][$key] = $localeData;
            }
        }

        return $data;
    }
}
