<?php

namespace App\Repositories\Eloquent;

use App\Data\DynamicFilterData;
use App\Models\Property\Property;
use App\Models\Property\PropertyAmenity;
use App\Models\Property\PropertyTranslation;
use App\Models\Property\PropertyKeyword;
use App\Models\Property\PropertyAttributeValue;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\Services\AutoFIlterAndSortService;
use App\Traits\FileManagerTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class PropertyRepository implements PropertyRepositoryInterface
{
    use FileManagerTrait;

    public function __construct(
        private readonly Property $model,
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
        ])->toArray();

        DB::beginTransaction();

        if (!isset($createData['currency_id'])) {
            $createData['currency_id'] = config('app.web_config.system_default_currency');
        }

        $createdModel = $this->model->create($createData);

        foreach (($data['locales'] ?? []) as $localeData) {
            $locale = $localeData['locale'];

            $translateRecord =  Arr::except($localeData, [
                'meta_keywords',
                'attributes'
            ]);
            $translateRecord['property_id'] = $createdModel->id;

            PropertyTranslation::create($translateRecord);
        }

        // store attributes
        for ($attributeIndex = 0; $attributeIndex < count(($data['attributes'] ?? [])); $attributeIndex++) {
            $attribute = $data['attributes'][$attributeIndex];
            PropertyAttributeValue::create([
                'property_id' => $createdModel->id,
                'attribute_id' => $attribute['custom_attribute_id'],
                'value' => $attribute['value'],
            ]);
        }

        // store meta keywords
        for ($keywordIndex = 0; $keywordIndex < count(($localeData['meta_keywords'] ?? [])); $keywordIndex++) {
            $keyword = $localeData['meta_keywords'][$keywordIndex];
            PropertyKeyword::create([
                'property_id' => $createdModel->id,
                'keyword' => $keyword,
            ]);
        }

        // store amenities
        $createdModel->amenities()->attach($data['amenities']);
        $createdModel->updateCachedAmenities();
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
        $model->update($updateData);

        foreach (($data['locales'] ?? []) as $localeData) {
            $locale = $localeData['locale'];

            $translateRecord =  Arr::except($localeData, [
                'meta_keywords',
                'attributes'
            ]);
            $translateRecord['property_id'] = $model->id;

            $oldTranslation = PropertyTranslation::where('property_id', $model->id)->where('locale', $locale)->first();
            if ($oldTranslation) {
                PropertyTranslation::where('id', $oldTranslation->id)->update($translateRecord);
            } else {
                PropertyTranslation::create($translateRecord);
            }
        }

        // update attributes
        for ($attributeIndex = 0; $attributeIndex < count(($data['attributes'] ?? [])); $attributeIndex++) {
            $attribute = $data['attributes'][$attributeIndex];
            $record = [
                'property_id' => $model->id,
                'attribute_id' => $attribute['custom_attribute_id'],
                // 'locale' => $locale,
                'value' => $attribute['value'],
            ];
            PropertyAttributeValue::updateOrCreate([
                'property_id' => $model->id,
                'attribute_id' => $attribute['custom_attribute_id'],
                // 'locale' => $locale,
            ], $record);
        }

        // update meta keywords
        // $keywords = collect(PropertyKeyword::where('property_id', $model->id)->where('locale', $locale)->pluck('keyword'))->toArray();
        $keywords = collect(PropertyKeyword::where('property_id', $model->id)->pluck('keyword'))->toArray();
        $requestKeyword = $data['meta_keywords'] ?? [];

        $toDelete = array_values(array_diff($keywords, $requestKeyword));
        $toInsert = array_values(array_diff($requestKeyword, $keywords));
        PropertyKeyword::where('property_id', $model->id)->whereIn('keyword', $toDelete)->delete();
        foreach ($toInsert as $keyword) {
            $record = [
                'property_id' => $model->id,
                // 'locale' => $locale,
                'keyword' => $keyword,
            ];
            PropertyKeyword::create($record);
        }

        // update amenities
        // // $amenities = collect(PropertyAmenity::where('property_id', $model->id)->pluck('id'))->toArray();
        // $amenities = collect($model->amenities)->pluck('id')->toArray();
        // $requestAmenities = $data['amenities'] ?? [];

        // $toDelete = array_values(array_diff($amenities, $requestAmenities));
        // $toInsert = array_values(array_diff($requestAmenities, $amenities));

        // PropertyAmenity::where('property_id', $model->id)->whereIn('amenity_id', $toDelete)->delete();
        // foreach ($toInsert as $keyword) {
        //     $record = [
        //         'property_id' => $model->id,
        //         'amenity_id' => $keyword,
        //     ];
        //     PropertyAmenity::create($record);
        // }

        $model->amenities()->sync($data['amenities'] ?? []);
        $model->updateCachedAmenities();
        DB::commit();
        $model->refresh();
        return  $model;
    }

    public function delete(Model $model): bool
    {
        $model->delete();
        $propertyId = $model->id ?? null;

        if ($propertyId) {
            $folderPath = PROPERTY_IMAGE_NAME . "/{$propertyId}";
            if (Storage::disk('asset')->exists($folderPath)) {
                Storage::disk('asset')->deleteDirectory($folderPath);
            }
        }
        // $this->deleteFile(filePath: $filePath, disk: 'asset');
        return true;
    }

    public function destroy(Model $model): bool
    {
        $model->forceDelete();
        // $filePath = PROPERTY_IMAGE_NAME . '/' . $model->image;
        // $this->deleteFile(filePath: $filePath, disk: 'asset');
        $propertyId = $model->id ?? null;
        if ($propertyId) {
            $folderPath = PROPERTY_IMAGE_NAME . "/{$propertyId}";
            if (Storage::disk('asset')->exists($folderPath)) {
                Storage::disk('asset')->deleteDirectory($folderPath);
            }
        }

        return true;
    }

    public function proceedImage($file, string $propertyId, string|null $oldFileName = null): bool|string
    {
        $folderPath = PROPERTY_IMAGE_NAME . "/{$propertyId}/images";
        if ($oldFileName) {
            $image =  $this->updateFile(dir: $folderPath . '/', oldFile: $oldFileName, file: $file, disk: 'asset');
        } else {
            $image =  $this->upload(dir: $folderPath . '/', file: $file, disk: 'asset');
        }
        return $image;
    }

    public function proceedImageDelete($file, string $propertyId, string|null $oldFileName = null)
    {
        $folderPath = PROPERTY_IMAGE_NAME . "/{$propertyId}/images";
        return  $this->deleteFile(filePath: $folderPath . '/' . $oldFileName, disk: 'asset');
    }


    public function proceedImageDeleteByName(string $propertyId, string $name)
    {
        $folderPath = PROPERTY_IMAGE_NAME . "/{$propertyId}/images";
        return  $this->deleteFile(filePath: $folderPath . '/' . $name, disk: 'asset');
    }
}
