<?php

namespace App\Repositories\Eloquent;

use App\Data\DynamicFilterData;
use App\Models\Property\PropertyGallery;
use App\Repositories\Contracts\PropertyGalleryRepositoryInterface;
use App\Services\AutoFIlterAndSortService;
use App\Traits\FileManagerTrait;
use Illuminate\Database\Eloquent\Model;

class PropertyGalleryRepository implements PropertyGalleryRepositoryInterface
{
    use FileManagerTrait;

    public function __construct(
        private readonly PropertyGallery $model,
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
        $createdModel = $this->model->create($data);
        return $createdModel;
    }

    public function show(Model $model): Model
    {
        return $model;
    }

    public function update(Model $model, array $data): Model
    {
        $model->update($data);
        $model->refresh();
        return  $model;
    }

    public function delete(Model $model): bool
    {
        $model->delete();
        $filePath = PROPERTY_IMAGE_NAME . '/' . $model->image;
        $this->deleteFile(filePath: $filePath, disk: 'asset');
        return true;
    }

    public function destroy(Model $model): bool
    {
        $model->forceDelete();
        $filePath = PROPERTY_IMAGE_NAME . '/' . $model->image;
        $this->deleteFile(filePath: $filePath, disk: 'asset');
        return true;
    }

    public function proceedImage($file, string|null $oldFileName = null): bool|string
    {
        $folderPath = PROPERTY_IMAGE_NAME;
        if ($oldFileName) {
            $image =  $this->updateFile(dir: $folderPath . '/', oldFile: $oldFileName, file: $file, disk: 'asset');
        } else {
            $image =  $this->upload(dir: $folderPath . '/', file: $file, disk: 'asset');
        }
        return $image;
    }

    public function proceedImageDelete($file, string|null $oldFileName = null)
    {
        $folderPath = PROPERTY_IMAGE_NAME;
        return  $this->deleteFile(filePath: $folderPath . '/' . $oldFileName, disk: 'asset');
    }
}
