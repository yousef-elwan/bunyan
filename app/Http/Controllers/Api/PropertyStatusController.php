<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PropertyStatusResource;
use App\Services\AutoFIlterAndSortService;
use App\Repositories\Contracts\PropertyStatusRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PropertyStatusController extends Controller
{

    public function __construct(
        protected PropertyStatusRepositoryInterface $repo,
    ) {}

    public function index(Request $request)
    {

        // $user = Auth::user();

        $result = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->repo, 'getList'],
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option)  {
                $query->with(['translations']);

                // if ($user->is_admin) {
                //     $query->whereIn('id', ['active', 'rejected']);
                // } else {
                //     $query->whereIn('id', ['active', 'inactive']);
                // }
            },
        );

        $result['data'] = collect($result['data'])->map(function ($propertyStatus) {
            return (new PropertyStatusResource($propertyStatus))->withFields(request()->get('fields'));
        })->toArray();

        return  successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }
}
