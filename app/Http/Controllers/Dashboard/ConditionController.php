<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Condition\DeleteRequest;
use App\Http\Requests\Dashboard\Condition\EditRequest;
use App\Http\Requests\Dashboard\Condition\StoreRequest;
use App\Http\Resources\Web\PropertyConditionResource;
use App\Models\Lang;
use App\Models\Property\Property;
use App\Services\AutoFIlterAndSortService;
use App\Models\PropertyCondition\PropertyCondition;
use App\Repositories\Contracts\PropertyConditionRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConditionController extends Controller
{
    public function __construct(
        protected  PropertyConditionRepositoryInterface $repo
    ) {}

    public function index()
    {
        $user = auth()->user();
        $langs = Lang::active()->get();


        $breadcrumbs = [
            ['name' => __('dashboard/layouts.aside.dashboard'), 'url' => route('dashboard.home')],
            ['name' => __('dashboard/layouts.aside.condition'), 'url' => null],
        ];

        return view('dashboard.pages.conditions.list', compact('langs', 'user', 'breadcrumbs'));
    }
    public function create(PropertyCondition $condition)
    {
        $user = auth()->user();
        $langs = Lang::active()->get();

        $breadcrumbs = [
            ['name' => __('dashboard/layouts.aside.dashboard'), 'url' => route('dashboard.home')],
            ['name' => __('dashboard/layouts.aside.condition'), 'url' => route('dashboard.condition.index')],
        ];

        return view('dashboard.pages.conditions.create', compact('langs', 'condition', 'user', 'breadcrumbs'));
    }
    public function edit(string  $locale,  PropertyCondition $condition)
    {
        $user = auth()->user();
        $langs = Lang::active()->get();
        $condition->load([
            'translations',
        ]);

        $breadcrumbs = [
            ['name' => __('dashboard/layouts.aside.dashboard'), 'url' => route('dashboard.home')],
            ['name' => __('dashboard/layouts.aside.condition'), 'url' => route('dashboard.condition.index')],
        ];

        return view('dashboard.pages.conditions.edit', compact('langs', 'condition', 'user', 'breadcrumbs'));
    }
    public function update(EditRequest $request, PropertyCondition $condition)
    {
        $validated = $request->validated();

        $model = $this->repo->update($condition, $validated);
        return  successResponse(
            message: translate('messages.updated_successfully'),
            data: $model,
        );
    }

    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $item = $this->repo->store($validated);
            return  successResponse(
                message: translate('messages.added_successfully'),
                data: $item
            );
        } catch (\Exception $e) {
            report($e);
            return errorResponse(
                message: trans('messages.generic.generic_error_try_again'),
                state: 401,
            );
        }
    }

    public function search(Request $request)
    {
        $result = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->repo, 'getList'],
            // paginationFormate: PaginationFormateEnum::separated,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->select('property_conditions.*');
                $query->with([
                    'translations',
                ]);
            },
        );
        $data = collect($result['data'])->map(function ($property_conditions) {
            return (new PropertyConditionResource($property_conditions))->withFields(request()->get('fields'));
        })->toArray();

        return successResponse('', data: $data, pagination: $result['pagination']);
    }

    public function destroy(DeleteRequest $request, PropertyCondition $condition)
    {
        if (Property::where('condition_id', $condition->id)->count() > 0) {
            return errorResponse(
                message: trans('messages.condition.cannot_delete_it_is_in_use'),
                state: 403,
            );
        }
        $this->repo->destroy($condition);
        return  successResponse(
            message: translate('messages.deleted_successfully'),
        );
    }
}
