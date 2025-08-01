<?php

namespace App\Repositories\Eloquent;

use App\Data\DynamicFilterData;
use App\Models\Slider\Slider;
use App\Models\Slider\SliderTranslation;
use App\Repositories\Contracts\SliderRepositoryInterface;
use App\Services\AutoFIlterAndSortService;
use App\Traits\FileManagerTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class SliderRepository  implements SliderRepositoryInterface
{

    use FileManagerTrait;


    public function __construct(
        private readonly Slider $model,

    ) {}

    /**
     * get data with dynamic options.
     *
     * @param DynamicFilterData $dynamicFilterData
     **/
    public function getList(DynamicFilterData $dynamicFilterData): array
    {
        return (new AutoFIlterAndSortService($this->model))->dynamicFilter($dynamicFilterData);
    }

    public function store(array $data): Model
    {
        $createData = collect($data)->except([
            'locales',
        ]);

        DB::beginTransaction();
        $createdModel = $this->model->create($createData->toArray());

        $translateRecords = [];

        for ($i = 0; $i < count(($data['locales'] ?? [])); $i++) {
            $localeData = $data['locales'][$i];
            $translateRecord =  $localeData;
            $translateRecord['slider_id'] = $createdModel->id;
            $translateRecords[] = $translateRecord;
            $locale = $localeData['locale'];
        }
        foreach ($translateRecords as $translateRecord) {
            SliderTranslation::create($translateRecord);
        }
        DB::commit();
        return $createdModel;
    }

    public function show(Model $model): Model
    {
        return $model;
    }

    public function update(Model $model, array $data): Model
    {

        $updateData = Arr::except($data, [
            'locales',
        ]);

        DB::beginTransaction();
        $effectedRows = $model->update($updateData);

        foreach (($data['locales'] ?? []) as $localeData) {
            $locale = $localeData['locale'];

            $translateRecord =  $localeData;
            $translateRecord['slider_id'] = $model->id;
            $oldTranslation = SliderTranslation::where('slider_id', $model->id)->where('locale', $locale)->first();
            if ($oldTranslation) {
                SliderTranslation::where('id', $oldTranslation->id)->update($translateRecord);
            } else {
                SliderTranslation::create($translateRecord);
            }
        }

        DB::commit();
        $model->refresh();
        return  $model;
    }

    public function delete(Model $model): bool
    {
        $model->delete();
        $filePath = SLIDER_IMAGE_NAME . '/' . $model->image;
        $this->deleteFile(filePath: $filePath, disk: 'asset');
        return true;
    }

    public function destroy(Model $model): bool
    {
        $model->forceDelete();
        $filePath = SLIDER_IMAGE_NAME . '/' . $model->image;
        $this->deleteFile(filePath: $filePath, disk: 'asset');
        return true;
    }


    public function proceedImage($file, string|null $oldFileName = null): bool|string
    {
        $folderPath = SLIDER_IMAGE_NAME;
        if ($oldFileName) {
            $image =  $this->updateFile(dir: $folderPath . '/', oldFile: $oldFileName, file: $file, disk: 'asset');
        } else {
            $image =  $this->upload(dir: $folderPath . '/', file: $file, disk: 'asset');
        }
        return $image;
    }

    public function proceedImageDelete($file, string|null $oldFileName = null)
    {
        $folderPath = SLIDER_IMAGE_NAME;
        return  $this->deleteFile(filePath: $folderPath . '/' . $oldFileName, disk: 'asset');
    }
}
