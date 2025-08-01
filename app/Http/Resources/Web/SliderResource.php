<?php

namespace App\Http\Resources\Web;

use App\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class SliderResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function resolveData(Request $request)
    {
        $data = \Illuminate\Http\Resources\Json\JsonResource::toArray($request);

        $data['title'] = myStripTags($data['title']);
        $data['sub_title'] = myStripTags($data['sub_title']);
        $data['button_text'] = myStripTags($data['button_text']);
        $data['content'] = myStripTags($data['content']);


        if (array_key_exists('translations', $data)) {
            $data['translations'] = collect($data['translations'])->map(function ($locale) {
                $locale['title'] = myStripTags($locale['title']);
                $locale['sub_title'] = myStripTags($locale['sub_title']);
                $locale['button_text'] = myStripTags($locale['button_text']);
                return collect($locale)->only([
                    'locale',
                    'title',
                    'sub_title',
                    'url',
                    'button_text',
                    'content'
                ]);
            })->groupBy('locale')->all();
        }
        foreach (array_keys($data['translations']) as $key) {
            $localeData = $data['translations'][$key][0];
            unset($localeData['locale']);
            $data['translations'][$key] = $localeData;
        }

        if (array_key_exists('category', $data)) {
            if (isset($data['category'])) {
                $data['image_url'] = $data['category']['random_item']['image_url'];
            }
        }

        $data = collect($data)
            ->only([
                'id',
                'from_time',
                'to_time',
                'image_url',
                'url',
                'title',
                'sub_title',
                'button_text',
                'background_color',
                'resource_type',
                'resource_id',
                'translations',
                'content',
            ])->all();

        return $data;
    }
}
