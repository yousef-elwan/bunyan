<?php

namespace App\Repositories\Eloquent;

use App\Models\BusinessSetting;
use App\Repositories\Contracts\BusinessSettingRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class BusinessSettingRepository implements BusinessSettingRepositoryInterface
{
    public function __construct(protected BusinessSetting $model) {}

    public function find(string $id): ?BusinessSetting
    {
        return $this->model->where('id', $id)->first();
    }

    public function all(): array
    {

        $envAppName =  strtolower(config('app.name'));

        $web_config = [];
        $this->model->select(['type', 'value'])->get()->each(function ($item) use (&$web_config) {
            $web_config[$item['type']] = $item['value'];
        });

        /** @var \Illuminate\Filesystem\FilesystemAdapter $assetDisk */
        $assetDisk = Storage::disk('asset');


        $defaultImageFolderUrl = asset(DEFAULTS_IMAGE_NAME);

        // $web_config['meta_description'] = Cache::rememberForever("about_us_$locale", function () use ($locale) {
        //     return AboutUsTranslation::where('locale', $locale)->first() ?? AboutUsTranslation::first();
        // })?->content ?? "";

        if (isset($web_config['company_logo'])) {
            $web_config['company_logo'] = $assetDisk->url('images/' . $web_config['company_logo']);
        }
        if (isset($web_config['company_fav_icon'])) {
            $web_config['company_fav_icon'] = $assetDisk->url('images/' . $web_config['company_fav_icon']);
        }
        if (isset($web_config['footer_logo'])) {
            $web_config['footer_logo'] = $assetDisk->url('images/' . $web_config['footer_logo']);
        }
        if (isset($web_config['about_us_image'])) {
            $web_config['about_us_image'] = $assetDisk->url(ABOUT_IMAGE_NAME . '/' . $web_config['about_us_image']);
        }
        if (isset($web_config['default_brand_image'])) {
            $web_config['default_brand_image'] = $defaultImageFolderUrl . "/" . $web_config['default_brand_image'];
        }
        if (isset($web_config['default_category_image'])) {
            $web_config['default_category_image'] = $defaultImageFolderUrl . "/" . $web_config['default_category_image'];
        }
        if (isset($web_config['default_property_image'])) {
            $web_config['default_property_image'] = $defaultImageFolderUrl . "/" . $web_config['default_property_image'];
        }
        if (isset($web_config['default_city_image'])) {
            $web_config['default_city_image'] = $defaultImageFolderUrl . "/" . $web_config['default_city_image'];
        }
        if (isset($web_config['default_item_image'])) {
            $web_config['default_item_image'] = $defaultImageFolderUrl . "/" . $web_config['default_item_image'];
        }
        if (isset($web_config['default_service_image'])) {
            $web_config['default_service_image'] = $defaultImageFolderUrl . "/" . $web_config['default_service_image'];
        }
        if (isset($web_config['default_feature_image'])) {
            $web_config['default_feature_image'] = $defaultImageFolderUrl . "/" . $web_config['default_feature_image'];
        }
        if (isset($web_config['default_statistic_image'])) {
            $web_config['default_statistic_image'] = $defaultImageFolderUrl . "/" . $web_config['default_statistic_image'];
        }
        if (isset($web_config['default_blog_image'])) {
            $web_config['default_blog_image'] = $defaultImageFolderUrl . "/" . $web_config['default_blog_image'];
        }
        if (isset($web_config['default_rewad_image'])) {
            $web_config['default_rewad_image'] = $defaultImageFolderUrl . "/" . $web_config['default_rewad_image'];
        }
        if (isset($web_config['default_partner_image'])) {
            $web_config['default_partner_image'] = $defaultImageFolderUrl . "/" . $web_config['default_partner_image'];
        }
        if (isset($web_config['default_portfolio_image'])) {
            $web_config['default_portfolio_image'] = $defaultImageFolderUrl . "/" . $web_config['default_portfolio_image'];
        }
        if (isset($web_config['default_sponsor_image'])) {
            $web_config['default_sponsor_image'] = $defaultImageFolderUrl . "/" . $web_config['default_sponsor_image'];
        }
        if (isset($web_config['default_team_image'])) {
            $web_config['default_team_image'] = $defaultImageFolderUrl . "/" . $web_config['default_team_image'];
        }
        if (isset($web_config['default_slider_image'])) {
            $web_config['default_slider_image'] = $defaultImageFolderUrl . "/" . $web_config['default_slider_image'];
        }
        if (isset($web_config['default_portfolio_image'])) {
            $web_config['default_portfolio_image'] = $defaultImageFolderUrl . "/" . $web_config['default_portfolio_image'];
        }
        if (isset($web_config['default_user_image'])) {
            $web_config['default_user_image'] = $defaultImageFolderUrl . "/" . $web_config['default_user_image'];
        }
        if (isset($web_config['cookie_setting'])) {
            $web_config['cookie_setting'] = (array) json_decode($web_config['cookie_setting']);
        }

        $web_config['cookie_popup_banner_name'] = $envAppName . '_popup_banner';
        $web_config['cookie_consent_name'] = $envAppName . '_cookie_consent';
        $web_config['cookie_order_by_whatsapp_name'] = $envAppName . 'order_by_whatsapp';

        $web_config['asset_url'] = $assetDisk->url("");

        return $web_config;
    }

    public function update(string $id, array $data): bool
    {
        return $this->model->where('id', $id)->update($data);
    }

    public function updateAboutUsImage($image)
    {
        return BusinessSetting::where(['type' => 'about_us_image'])->update(['value' => $image]);
    }
}
