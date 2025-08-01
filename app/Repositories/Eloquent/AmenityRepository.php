<?php

namespace App\Repositories\Eloquent;

use App\Data\DynamicFilterData;
use App\Models\Amenity\Amenity;
use App\Models\Amenity\AmenityTranslation;
use App\Repositories\Contracts\AmenityRepositoryInterface;
use App\Services\AutoFIlterAndSortService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class AmenityRepository implements AmenityRepositoryInterface
{

    // public function __construct(
    //     private readonly Amenity $model,
    // ) {}

    private Amenity|null $model = null;

    public function __construct()
    {
        $this->model = new Amenity();
    }

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

            if (!isset($translateRecord['name'])) {
                continue;
            }

            $translateRecord['amenity_id'] = $createdModel->id;
            $translateRecords[] = $translateRecord;

            $locale = $localeData['locale'];
        }
        foreach ($translateRecords as $translateRecord) {
            AmenityTranslation::create($translateRecord);
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
            $translateRecord['amenity_id'] = $model->id;
            $oldTranslation = AmenityTranslation::where('amenity_id', $model->id)->where('locale', $locale)->first();
            if ($oldTranslation) {
                AmenityTranslation::where('id', $oldTranslation->id)->update($translateRecord);
            } else {
                AmenityTranslation::create($translateRecord);
            }
        }

        DB::commit();
        $model->refresh();
        return  $model;
    }

    public function delete(Model $model): bool
    {
        $deleted = $model->delete();
        return $deleted;
    }

    public function destroy(Model $model): bool
    {
        $deleted = $model->forceDelete();
        return $deleted;
    }
}
