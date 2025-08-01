<?php

namespace App\Repositories\Eloquent;

use App\Data\DynamicFilterData;
use App\Models\PropertyFAQ\PropertyFAQ;
use App\Models\PropertyFAQ\PropertyFAQTranslation;
use App\Repositories\Contracts\PropertyFAQRepositoryInterface;
use App\Services\AutoFIlterAndSortService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class PropertyFAQRepository implements PropertyFAQRepositoryInterface
{
    public function __construct(
        private readonly PropertyFAQ $model,
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

        foreach (($data['locales'] ?? []) as $localeData) {
            $locale = $localeData['locale'];

            $translateRecord = Arr::except($localeData, []);
            $translateRecord['property_faq_id'] = $createdModel->id;

            PropertyFAQTranslation::create($translateRecord);
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
        $model->update($updateData);

        foreach (($data['locales'] ?? []) as $localeData) {
            $locale = $localeData['locale'];

            $translateRecord = Arr::except($localeData, []);
            $translateRecord['property_faq_id'] = $model->id;

            $oldTranslation = PropertyFAQTranslation::where('property_faq_id', $model->id)->where('locale', $locale)->first();
            if ($oldTranslation) {
                PropertyFAQTranslation::where('id', $oldTranslation->id)->update($translateRecord);
            } else {
                PropertyFAQTranslation::create($translateRecord);
            }
        }

        DB::commit();
        $model->refresh();
        return $model;
    }

    public function delete(Model $model): bool
    {
        $model->delete();
        return true;
    }

    public function destroy(Model $model): bool
    {
        $model->forceDelete();
        return true;
    }
}
