<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CustomAttributeResource;
use App\Services\AutoFIlterAndSortService;
use App\Repositories\Contracts\CustomAttributeRepositoryInterface;
use Illuminate\Http\Request;

class CustomAttributeController extends Controller
{

    public function __construct(
        protected CustomAttributeRepositoryInterface $customAttributeRepositoryInterface,
    ) {}

    public function index(Request $request)
    {

        $result = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->customAttributeRepositoryInterface, 'getList'],
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with(['translations', 'customAttributeValues']);
            },
        );

        $result['data'] = collect($result['data'])->map(function ($property) {
            return (new CustomAttributeResource($property))->withFields(request()->get('fields'));
        })->toArray();


        return  successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }
}
