<?php

namespace App\Repositories\Eloquent;

use App\Models\AboutUsTranslation;
use App\Models\BusinessSetting;
use App\Repositories\Contracts\AboutUsRepositoryInterface;
use App\Traits\FileManagerTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AboutUsRepository implements AboutUsRepositoryInterface
{

    use FileManagerTrait;


    public function store(array $data)
    {

        DB::beginTransaction();
        if (isset($data['image'])) {
            BusinessSetting::where(['type' => 'about_us_image'])->update([
                'value' => $data['image']
            ]);
        }

        foreach (($data['locales'] ?? []) as $aboutUsTranslate) {
            $locale = $aboutUsTranslate['locale'];

            $aboutUsTranslateData =  Arr::only($aboutUsTranslate, [
                'locale',
                'content',
                'title',
                'meta_title',
                'meta_description'
            ]);
            $oldAboutUsTranslation = AboutUsTranslation::where('locale', $locale)->first();
            if ($oldAboutUsTranslation) {
                AboutUsTranslation::where('locale', $locale)->update($aboutUsTranslateData);
            } else {
                AboutUsTranslation::create($aboutUsTranslateData);
            }
        }

        DB::commit();
    }

    public function proceedImage($file, string|null $oldFileName = null): bool|string
    {
        $folderPath = ABOUT_IMAGE_NAME;
        if ($oldFileName) {
            $image =  $this->updateFile(dir: $folderPath . '/', oldFile: $oldFileName, file: $file, disk: 'asset');
        } else {
            $image =  $this->upload(dir: $folderPath . '/', file: $file, disk: 'asset');
        }
        return $image;
    }

    public function proceedImageDelete($file, string|null $oldFileName = null)
    {
        $folderPath = ABOUT_IMAGE_NAME;
        return  $this->deleteFile(filePath: $folderPath . '/' . $oldFileName, disk: 'asset');
    }
}
