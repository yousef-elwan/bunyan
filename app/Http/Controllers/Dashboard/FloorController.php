<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\PaginationFormateEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Floor\DeleteRequest;
use App\Http\Requests\Dashboard\Floor\EditeRequest;
use App\Http\Requests\Dashboard\Floor\StoreRequest;
use App\Http\Resources\Web\FloorResource;
use App\Models\Floor\Floor;
use App\Models\Lang;
use App\Models\Property\Property;
use App\Repositories\Contracts\FloorRepositoryInterface;
use App\Services\AutoFIlterAndSortService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FloorController extends Controller
{

    public function __construct(protected FloorRepositoryInterface $floorRepositoryInterface) {}
    public function index()
    {
        $user = auth()->user();
        $langs = Lang::active()->get();

        $breadcrumbs = [
            ['name' => __('dashboard/layouts.aside.dashboard'), 'url' => route('dashboard.home')],
            ['name' => __('dashboard/layouts.aside.floor'), 'url' => null],
        ];

        return view('dashboard.pages.floor.list', compact('langs', 'user', 'breadcrumbs'));
    }
    public function search(Request $request)
    {
        $result = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->floorRepositoryInterface, 'getList'],
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->select('floors.*');
                $query->with([
                    'translations',
                ]);
            },
        );
        $data = collect($result['data'])->map(function ($floor) {
            return (new FloorResource($floor))->withFields(request()->get('fields'));
        })->toArray();

        return successResponse('', data: $data, pagination: $result['pagination']);
    }
    public function edit(string $locale, Floor $floor)
    {
        $user = auth()->user();
        $langs = Lang::active()->get();
        $floor->load([
            'translations',
        ]);


        $breadcrumbs = [
            ['name' => __('dashboard/layouts.aside.dashboard'), 'url' => route('dashboard.home')],
            ['name' => __('dashboard/layouts.aside.floor'), 'url' => route('dashboard.floor.index')],
        ];

        return view('dashboard.pages.floor.edit', compact('langs', 'user', 'floor', 'breadcrumbs'));
    }
    public function update(EditeRequest $request,  Floor $floor)
    {
        $validated = $request->validated();

        $categoryModel = $this->floorRepositoryInterface->update($floor, $validated);
        return  successResponse(
            message: translate('messages.updated_successfully'),
            data: $categoryModel,
        );
    }
    public function create(string $locale, Floor $floor)
    {
        $user = auth()->user();
        $langs = Lang::active()->get();


        $breadcrumbs = [
            ['name' => __('dashboard/layouts.aside.dashboard'), 'url' => route('dashboard.home')],
            ['name' => __('dashboard/layouts.aside.floor'), 'url' => route('dashboard.floor.index')],
        ];

        return view('dashboard.pages.floor.create', compact('langs', 'floor', 'user', 'breadcrumbs'));
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $item = $this->floorRepositoryInterface->store($validated);
            return  successResponse(
                message: translate('messages.added_successfully'),
                data: $item
            );
        } catch (\Exception $e) {
            return errorResponse(
                message: trans('messages.generic.generic_error_try_again'),
                state: 401,
            );
        }
    }
    public function destroy(DeleteRequest $request, Floor $floor)
    {
        if (Property::where('floor_id', $floor->id)->count() > 0) {
            return errorResponse(
                message: trans('messages.floor.cannot_delete_it_is_in_use'),
                state: 403,
            );
        }
        $this->floorRepositoryInterface->destroy($floor);
        return  successResponse(
            message: translate('messages.deleted_successfully'),
        );
    }
}
