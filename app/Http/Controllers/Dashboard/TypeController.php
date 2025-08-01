<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Type\DeleteRequest;
use App\Http\Requests\Dashboard\Type\EditRequest;
use App\Http\Requests\Dashboard\Type\StoreRequest;
use App\Http\Resources\Web\TypeResource;
use App\Models\Lang;
use App\Models\Property\Property;
use App\Models\Type\Type;
use App\Repositories\Contracts\TypeRepositoryInterface;
use App\Services\AutoFIlterAndSortService;
use Illuminate\Http\Request;

class TypeController extends Controller
{
    public function __construct(
        protected TypeRepositoryInterface $typeRepositoryInterface
    ) {}
    public function index()
    {
        $user = auth()->user();
        $langs = Lang::active()->get();

        $breadcrumbs = [
            ['name' => __('dashboard/layouts.aside.dashboard'), 'url' => route('dashboard.home')],
            ['name' => __('dashboard/layouts.aside.type'), 'url' => null],
        ];


        return view('dashboard.pages.type.list', compact('langs', 'user', 'breadcrumbs'));
    }

    public function search(Request $request)
    {
        $result = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->typeRepositoryInterface, 'getList'],
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->select('types.*');
                $query->with([
                    'translations',
                ]);
            },
        );
        $data = collect($result['data'])->map(function ($type) {
            return (new TypeResource($type))->withFields(request()->get('fields'));
        })->toArray();

        return successResponse('', data: $data, pagination: $result['pagination']);
    }
    public function update(EditRequest $request, Type $type)
    {
        $validated = $request->validated();

        $model = $this->typeRepositoryInterface->update($type, $validated);
        return  successResponse(
            message: translate('messages.updated_successfully'),
            data: $model,
        );
    }
    public function edit(string  $locale, Type $type)
    {
        $user = auth()->user();
        $langs = Lang::active()->get();
        $type->load([
            'translations',
        ]);

        $breadcrumbs = [
            ['name' => __('dashboard/layouts.aside.dashboard'), 'url' => route('dashboard.home')],
            ['name' => __('dashboard/layouts.aside.type'), 'url' => route('dashboard.type.index')],
        ];

        return view('dashboard.pages.type.edit', compact('langs', 'type', 'user', 'breadcrumbs'));
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $item = $this->typeRepositoryInterface->store($validated);
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
    public function destroy(DeleteRequest $request, Type $type)
    {
        if (Property::where('type_id', $type->id)->count() > 0) {
            return errorResponse(
                message: trans('messages.type.cannot_delete_it_is_in_use'),
                state: 403,
            );
        }
        $this->typeRepositoryInterface->destroy($type);
        return  successResponse(
            message: translate('messages.deleted_successfully'),
        );
    }
    public function create(Type $type)
    {
        $user = auth()->user();
        $langs = Lang::active()->get();


        $breadcrumbs = [
            ['name' => __('dashboard/layouts.aside.dashboard'), 'url' => route('dashboard.home')],
            ['name' => __('dashboard/layouts.aside.type'), 'url' => route('dashboard.type.index')],
        ];

        return view('dashboard.pages.type.create', compact('langs', 'type', 'user', 'breadcrumbs'));
    }
}
