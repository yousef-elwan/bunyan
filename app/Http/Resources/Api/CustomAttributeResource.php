<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class CustomAttributeResource extends BaseJsonResource
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
                'category_id',
                'type',
                'name',
                'translations',
                'custom_attribute_values',
            ])->all();

        if (array_key_exists('translations', $data)) {
            $data['translations'] = collect($data['translations'])->map(function ($localeData) {
                $localeData = is_array($localeData) ? $localeData : $localeData->toArray();
                $localeData = collect($localeData)
                    ->only([
                        'locale',
                        'name',
                    ])->all();
                return $localeData;
            })->groupBy('locale')->all();

            foreach (array_keys($data['translations']) as $key) {
                $localeData = $data['translations'][$key][0];
                unset($localeData['locale']);
                $data['translations'][$key] = $localeData;
            }
        }

        if (array_key_exists('custom_attribute_values', $data)) {
            $data['custom_attribute_values'] = collect($data['custom_attribute_values'])->map(function ($value) {
                return [
                    'id' => $value['id'],
                    'value' => $value['value'],
                ];
            });
        }

        return $data;
    }
}
