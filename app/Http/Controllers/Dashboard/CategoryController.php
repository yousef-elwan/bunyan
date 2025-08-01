<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Category\DeleteRequest;
use App\Http\Requests\Dashboard\Category\EditeRequest;
use App\Http\Requests\Dashboard\Category\StoreRequest;
use App\Http\Resources\Web\CategoryResource;
use App\Models\Category\Category;
use App\Models\Lang;
use App\Models\Property\Property;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\AutoFIlterAndSortService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryRepositoryInterface $repo
    ) {}

    public function index()
    {
        $user = auth()->user();
        $langs = Lang::active()->get();

        $breadcrumbs = [
            ['name' => __('dashboard/layouts.aside.dashboard'), 'url' => route('dashboard.home')],
            ['name' => __('dashboard/layouts.aside.category'), 'url' => null],
        ];

        return view('dashboard.pages.category.list', compact('langs', 'user', 'breadcrumbs'));
    }
    public function search(Request $request)
    {
        $result = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->repo, 'getList'],
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->select('categories.*');
                $query->with([
                    'translations',
                ]);
            },
        );
        $data = collect($result['data'])->map(function ($category) {
            return (new CategoryResource($category))->withFields(request()->get('fields'));
        })->toArray();

        return successResponse('', data: $data, pagination: $result['pagination']);
    }
    public function edit(string  $locale, Category $category)
    {
        $user = auth()->user();
        $langs = Lang::active()->get();
        $category->load([
            'translations',
        ]);

        $breadcrumbs = [
            ['name' => __('dashboard/layouts.aside.dashboard'), 'url' => route('dashboard.home')],
            ['name' => __('dashboard/layouts.aside.category'), 'url' => route('dashboard.category.index')],
        ];

        return view('dashboard.pages.category.edit', compact('langs', 'user', 'category', 'breadcrumbs'));
    }
    public function update(EditeRequest $request, Category $category)
    {
        $validated = $request->validated();

        $hasOldImage = is_null($category->image);
        if (isset($validated['image']) && !is_null(isset($validated['image']))) {
            // new or update
            $fileName = $this->repo->proceedImage(file: $validated['image'], oldFileName: $category->image);
            $validated['image'] = $fileName;
        } else if ($hasOldImage && is_null(isset($validated['image']))) {
            // delete image.
            $this->repo->proceedImageDelete($category->image);
            $validated['image'] = null;
        }

        $categoryModel = $this->repo->update($category, $validated);
        return  successResponse(
            message: translate('messages.updated_successfully'),
            data: $categoryModel,
        );
    }
    public function create(Category $category)
    {
        $user = auth()->user();
        $langs = Lang::active()->get();

        $breadcrumbs = [
            ['name' => __('dashboard/layouts.aside.dashboard'), 'url' => route('dashboard.home')],
            ['name' => __('dashboard/layouts.aside.category'), 'url' => route('dashboard.category.index')],
        ];

        return view('dashboard.pages.category.create', compact('langs', 'category', 'user', 'breadcrumbs'));
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            if (isset($validated['image'])) {
                $fileName = $this->repo->proceedImage(file: $request['image']);
                $validated['image'] = $fileName;
            }

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
    public function destroy(DeleteRequest $request, Category $category)
    {
        if (Property::where('category_id', $category->id)->count() > 0) {
            return errorResponse(
                message: trans('messages.category.cannot_delete_it_is_in_use'),
                state: 403,
            );
        }
        $this->repo->destroy($category);
        return  successResponse(
            message: translate('messages.deleted_successfully'),
        );
    }
}
