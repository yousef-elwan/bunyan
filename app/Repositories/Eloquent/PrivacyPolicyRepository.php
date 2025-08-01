<?php

namespace App\Repositories\Eloquent;

use App\Models\PrivacyPolicyTranslation;
use App\Models\BusinessSetting;
use App\Repositories\Contracts\PrivacyPolicyRepositoryInterface;
use App\Traits\FileManagerTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PrivacyPolicyRepository implements PrivacyPolicyRepositoryInterface
{

    use FileManagerTrait;


    public function store(array $data)
    {

        DB::beginTransaction();
        if (isset($data['image'])) {
            BusinessSetting::where(['type' => 'privacy_policy_image'])->update([
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
            $oldPrivacyPolicyTranslation = PrivacyPolicyTranslation::where('locale', $locale)->first();
            if ($oldPrivacyPolicyTranslation) {
                PrivacyPolicyTranslation::where('locale', $locale)->update($aboutUsTranslateData);
            } else {
                PrivacyPolicyTranslation::create($aboutUsTranslateData);
            }
        }

        DB::commit();
    }

    public function proceedImage($file, string|null $oldFileName = null): bool|string
    {
        $folderPath = PRIVACY_POLICY_IMAGE_NAME;
        if ($oldFileName) {
            $image =  $this->updateFile(dir: $folderPath . '/', oldFile: $oldFileName, file: $file, disk: 'asset');
        } else {
            $image =  $this->upload(dir: $folderPath . '/', file: $file, disk: 'asset');
        }
        return $image;
    }

    public function proceedImageDelete($file, string|null $oldFileName = null)
    {
        $folderPath = PRIVACY_POLICY_IMAGE_NAME;
        return  $this->deleteFile(filePath: $folderPath . '/' . $oldFileName, disk: 'asset');
    }
}
