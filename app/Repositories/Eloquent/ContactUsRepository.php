<?php

namespace App\Repositories\Eloquent;

use App\Data\DynamicFilterData;
use App\Models\ContactUs;
use App\Repositories\Contracts\ContactUsRepositoryInterface;
use App\Services\AutoFIlterAndSortService;
use Illuminate\Database\Eloquent\Model;

class ContactUsRepository implements ContactUsRepositoryInterface
{

    public function __construct(
        private readonly ContactUs $model,

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
        $contact = $this->model->create($data);
        return $contact;
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
        $deleted = $model->delete();
        return $deleted;
    }

    public function destroy(Model $model): bool
    {
        $deleted = $model->forceDelete();
        return $deleted;
    }
}
