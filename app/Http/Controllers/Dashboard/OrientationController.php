<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Orientation\DeleteRequest;
use App\Http\Requests\Dashboard\Orientation\EditRequest;
use App\Http\Requests\Dashboard\Orientation\StoreRequest;
use App\Http\Resources\Web\OrientationResource;
use App\Models\Lang;
use App\Models\Orientation\Orientation;
use App\Models\Property\Property;
use App\Repositories\Contracts\OrientationRepositoryInterface;
use App\Services\AutoFIlterAndSortService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrientationController extends Controller
{
    public function __construct(
        protected  OrientationRepositoryInterface $repo
    ) {}

    public function index()
    {
        $user = auth()->user();
        $langs = Lang::active()->get();

        $breadcrumbs = [
            ['name' => __('dashboard/layouts.aside.dashboard'), 'url' => route('dashboard.home')],
            ['name' => __('dashboard/layouts.aside.orientation'), 'url' => null],
        ];

        return view('dashboard.pages.orientation.list', compact('langs', 'user', 'breadcrumbs'));
    }
    public function search(Request $request)
    {
        $result = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->repo, 'getList'],
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->select('orientations.*');
                $query->with([
                    'translations',
                ]);
            },
        );
        $data = collect($result['data'])->map(function ($orientation) {
            return (new OrientationResource($orientation))->withFields(request()->get('fields'));
        })->toArray();

        return successResponse('', data: $data, pagination: $result['pagination']);
    }
    public function update(EditRequest $request, Orientation $orientation)
    {
        $validated = $request->validated();

        $model = $this->repo->update($orientation, $validated);
        return  successResponse(
            message: translate('messages.updated_successfully'),
            data: $model,
        );
    }
    public function edit(string  $locale, Orientation $orientation)
    {
        $user = auth()->user();
        $langs = Lang::active()->get();
        $orientation->load([
            'translations',
        ]);

        $breadcrumbs = [
            ['name' => __('dashboard/layouts.aside.dashboard'), 'url' => route('dashboard.home')],
            ['name' => __('dashboard/layouts.aside.orientation'), 'url' => route('dashboard.orientation.index')],
        ];

        return view('dashboard.pages.orientation.edit', compact('langs', 'orientation', 'user', 'breadcrumbs'));
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
            return errorResponse(
                message: trans('messages.generic.generic_error_try_again'),
                state: 401,
            );
        }
    }
    public function destroy(DeleteRequest $request, Orientation $orientation)
    {
        if (Property::where('orientation_id', $orientation->id)->count() > 0) {
            return errorResponse(
                message: trans('messages.orientation.cannot_delete_it_is_in_use'),
                state: 403,
            );
        }
        $this->repo->destroy($orientation);
        return  successResponse(
            message: translate('messages.deleted_successfully'),
        );
    }
    public function create(Orientation $orientation)
    {
        $user = auth()->user();
        $langs = Lang::active()->get();

        $breadcrumbs = [
            ['name' => __('dashboard/layouts.aside.dashboard'), 'url' => route('dashboard.home')],
            ['name' => __('dashboard/layouts.aside.orientation'), 'url' => route('dashboard.orientation.index')],
        ];

        return view('dashboard.pages.orientation.create', compact('langs', 'orientation', 'user', 'breadcrumbs'));
    }
}
