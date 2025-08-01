<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\User\DeleteRequest;
use App\Models\Lang;
use App\Http\Requests\Dashboard\User\StoreRequest;
use App\Http\Requests\Dashboard\User\EditeRequest;
use App\Http\Resources\Web\UserResource;
use App\Models\Property\Property;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\AutoFIlterAndSortService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function __construct(
        protected UserRepositoryInterface $repo
    ) {}


    public function index()
    {
        $user = auth()->user();
        $langs = Lang::active()->get();

        $breadcrumbs = [
            ['name' => __('dashboard/layouts.aside.dashboard'), 'url' => route('dashboard.home')],
            ['name' => __('dashboard/layouts.aside.users'), 'url' => null],
        ];

        return view('dashboard.pages.user.list', compact('langs', 'user', 'breadcrumbs'));
    }
    public function search(Request $request)
    {
        $result = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->repo, 'getList'],
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->where('id', '!=', Auth::id());
                $query->withCount('properties');
            },
        );
        $data = collect($result['data'])->map(function ($user) {
            return (new userResource($user))->withFields(request()->get('fields'));
        })->toArray();

        return successResponse('', data: $data, pagination: $result['pagination']);
    }
    public function edit(string  $locale, user $user)
    {
        $user = auth()->user();
        $langs = Lang::active()->get();
        $user->load([]);
        $breadcrumbs = [
            ['name' => __('dashboard/layouts.aside.dashboard'), 'url' => route('dashboard.home')],
            ['name' => __('dashboard/layouts.aside.users'), 'url' => route('dashboard.home')],
        ];

        return view('dashboard.pages.user.edit', compact('langs', 'user', 'user', 'breadcrumbs'));
    }
    public function update(EditeRequest $request, user $user)
    {
        $validated = $request->validated();

        $hasOldImage = is_null($user->image);
        if (isset($validated['image']) && !is_null(isset($validated['image']))) {
            // new or update
            $fileName = $this->repo->proceedImage(file: $validated['image'], oldFileName: $user->image);
            $validated['image'] = $fileName;
        } else if ($hasOldImage && is_null(isset($validated['image']))) {
            // delete image.
            $this->repo->proceedImageDelete($user->image);
            $validated['image'] = null;
        }

        $userModel = $this->repo->update($user, $validated);
        return  successResponse(
            message: translate('messages.updated_successfully'),
            data: $userModel,
        );
    }
    public function create(user $user)
    {
        $user = auth()->user();
        $langs = Lang::active()->get();

        $breadcrumbs = [
            ['name' => __('dashboard/layouts.aside.dashboard'), 'url' => route('dashboard.home')],
            ['name' => __('dashboard/layouts.aside.users'), 'url' => route('dashboard.home')],
        ];

        return view('dashboard.pages.user.create', compact('langs', 'user', 'user', 'breadcrumbs'));
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
    public function destroy(DeleteRequest $request, user $user)
    {
        if (Property::where('user_id', $user->id)->count() > 0) {
            return errorResponse(
                message: trans('messages.user.cannot_delete_it_is_in_use'),
                state: 403,
            );
        }
        $this->repo->destroy($user);
        return  successResponse(
            message: translate('messages.deleted_successfully'),
        );
    }

    public function show(Request $request, User $user)
    {
        return successResponse(
            data: $user
        );
    }
}
