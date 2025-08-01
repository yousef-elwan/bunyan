<?php

namespace App\Http\Resources\Web;

use App\Http\Resources\BaseJsonResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PropertyResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function resolveData(Request $request): array
    {
        $data = \Illuminate\Http\Resources\Json\JsonResource::toArray($request);

        $data = collect($data)
            ->only([
                'id',
                'slug',
                'published_at',
                'published_at_formatted',
                'price',
                'price_display',
                'rooms_count',
                'showing_request_types',
                'size',
                'location',
                'view_count',
                'images',
                'city',
                'image_url',
                'content',
                'category',
                'orientation',
                'type',
                'amenities',
                'floor',
                'favorite',
                'contract_type',
                'condition',
                'available_from',
                'price_on_request',
                'property_attribute',
                'is_new',
                'currency',
                'available_times',
                'owner',
                'is_featured',
                'year_built',
                'video_url',
                'latitude',
                'longitude',
                'nearby',
                'faqs',
                'status',
                'status_id',
                'is_favorite',
                'user_id',
                'created_at',
                'is_blacklist',
                'translations',
                // add any other top‑level fields your blade needs
            ])->all();

        // Format nested arrays exactly as your view expects:
        $data['images'] = collect($data['images'] ?? [])->map(
            fn($img) =>
            $img['image_url'] ?? null
        )->all();
        $data['amenities'] = collect($data['amenities'] ?? [])->map(
            fn($amenity) =>
            $amenity['name'] ?? null
        )->all();


        if (array_key_exists('property_attribute', $data)) {

            $data['attribute'] = collect($data['property_attribute'] ?? [])->map(
                fn($attribute) =>
                [
                    'name' => $attribute['custom_attribute']['name'] ?? null,
                    'value' => $attribute['value'] ?? null
                ]
            )->all();

            unset($data['property_attribute']);
        }


        $data['faqs'] = collect($data['faqs'] ?? [])->map(
            fn($faq) =>
            [
                'question' => $faq['title'] ?? null,
                'answer' => $faq['content'] ?? null
            ]
        )->all();

        $data['available_times'] = collect($data['available_times'] ?? [])->map(
            fn($time) =>
            [
                'id' => $time['id'],
                'time' =>  $time['time']
            ]
        )->all();

        $data['showing_request_types'] = collect($data['showing_request_types'] ?? [])->map(
            fn($types) =>
            [
                'id' => $types['id'],
                'name' =>  $types['name']
            ]
        )->all();

        if (isset($data['floor']) && is_array($data['floor'])) {
            // If your floor field is an enum or model, extract its value
            $data['floor'] = $data['floor']['name'] ?? $data['floor'];
        }
        if (isset($data['type']) && is_array($data['type'])) {
            // If your type field is an enum or model, extract its value
            $data['type'] = $data['type']['name'] ?? $data['type'];
        }
        if (isset($data['city']) && is_array($data['city'])) {
            // If your city field is an enum or model, extract its value
            $data['city'] = $data['city']['name'] ?? $data['city'];
        }
        if (isset($data['type']) && is_array($data['type'])) {
            // If your type field is an enum or model, extract its value
            $data['type'] = $data['type']['name'] ?? $data['type'];
        }
        if (isset($data['condition']) && is_array($data['condition'])) {
            // If your condition field is an enum or model, extract its value
            $data['condition'] = $data['condition']['name'] ?? $data['condition'];
        }
        if (isset($data['contract_type']) && is_array($data['contract_type'])) {
            // If your contract_type field is an enum or model, extract its value
            $data['contract_type'] = $data['contract_type']['name'] ?? $data['contract_type'];
        }
        if (isset($data['category']) && is_array($data['category'])) {
            // If your category field is an enum or model, extract its value
            $data['category'] = $data['category']['name'] ?? $data['category'];
        }
        if (isset($data['orientation']) && is_array($data['orientation'])) {
            // If your orientation field is an enum or model, extract its value
            $data['orientation'] = $data['orientation']['name'] ?? $data['orientation'];
        }

        // if (isset($data['video_url'])) {
        //     $url = parse_url($data['video_url']);
        //     parse_str($url['query'], $query);
        //     $data['video_img_url'] = "https://img.youtube.com/vi/{$query['v']}/0.jpg" ?? null;
        // }

        if (isset($data['video_url'])) {
            $video_url = $data['video_url'];
            $video_id = extractYouTubeVideoId($video_url);
            $data['video_img_url'] = is_null($video_id) ? null : "https://img.youtube.com/vi/{$video_id}/0.jpg";
        }

        if (array_key_exists('owner', $data)) {
            if (is_array($data['owner'])) {
                // If your owner field is an enum or model, extract its value
                $data['owner'] = [
                    'id' => $data['owner']['id'] ?? null,
                    'name' => $data['owner']['name'] ?? null,
                    'mobile' => $data['owner']['mobile'] ?? null,
                    'email' =>  $data['owner']['email'] ?? null,
                    'image_url' => $data['owner']['image_url'],
                    'is_blacklisted' => $data['owner']['is_blacklisted'],
                    'blacklist_reason' => $data['owner']['blacklist_reason'],
                ];
            }
        }

        $data['owner_is_me'] = $data['user_id'] == Auth::id();

        if (isset($data['created_at'])) {
            // Format created_at to a readable format, e.g., 'Y-m-d H:i:s'
            // $data['created_at'] = Carbon::parse($data['created_at'])->format('Y-m-d H:i:s');
            $data['created_at'] = Carbon::parse($data['created_at'])->format('Y-m-d');
        }
        // (Repeat for any other nested or enum fields...)
        return $data;
    }
}
