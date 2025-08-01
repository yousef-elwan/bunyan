<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\FilterFnsEnum;
use App\Enums\PaginationFormateEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Property\Destroy;
use App\Http\Resources\Web\AmenityResource;
use App\Http\Resources\Web\CategoryResource;
use App\Http\Resources\Web\CityResource;
use App\Http\Resources\Web\ContractTypesResource;
use App\Http\Resources\Web\FloorResource;
use App\Http\Resources\Web\OrientationResource;
use App\Http\Resources\Web\PropertyConditionResource;
use App\Http\Resources\Web\PropertyResource;
use App\Http\Resources\Web\TypeResource;
use App\Models\Lang;
use App\Repositories\Contracts\AmenityRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\CityRepositoryInterface;
use App\Repositories\Contracts\ContractTypeRepositoryInterface;
use App\Repositories\Contracts\FloorRepositoryInterface;
use App\Repositories\Contracts\OrientationRepositoryInterface;
use App\Repositories\Contracts\PropertyConditionRepositoryInterface;
use App\Repositories\Contracts\PropertyReportRepositoryInterface;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\Repositories\Contracts\ShowingRequestRepositoryInterface;
use App\Repositories\Contracts\SliderRepositoryInterface;
use App\Repositories\Contracts\TypeRepositoryInterface;
use App\Http\Requests\Dashboard\Property\Store;
use App\Http\Requests\Dashboard\Property\Update;
use App\Http\Requests\Dashboard\Property\Upload;
use App\Http\Resources\Api\PropertyStatusResource;
use App\Http\Resources\Web\UserResource;
use App\Mail\NewPropertyNotification;
use App\Mail\PropertyRejectedStatusChanged;
use App\Models\Favorite;
use App\Models\Property\Property;
use App\Models\Property\PropertyGallery;
use App\Models\PropertyStatus\PropertyStatus;
use App\Models\User;
use App\Repositories\Contracts\PropertyStatusRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;
use App\Services\AutoFIlterAndSortService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use function Pest\Laravel\json;

class PropertyController extends Controller
{


    public function __construct(
        protected TypeRepositoryInterface $typeRepository,
        protected AmenityRepositoryInterface $amenityRepository,
        protected CategoryRepositoryInterface $categoryRepository,
        protected FloorRepositoryInterface $floorRepository,
        protected OrientationRepositoryInterface $orientationRepositoryInterface,
        protected CityRepositoryInterface $cityRepositoryInterface,
        protected PropertyReportRepositoryInterface $propertyReportRepositoryInterface,
        protected SliderRepositoryInterface $sliderRepositoryInterface,
        protected PropertyConditionRepositoryInterface $propertyConditionRepositoryInterface,
        protected ShowingRequestRepositoryInterface $showingRequestRepositoryInterface,
        protected ContractTypeRepositoryInterface $contractTypeRepositoryInterface,
        protected PropertyRepositoryInterface $propertyRepositoryInterface,
        protected UserRepositoryInterface $userRepositoryInterface,
        protected PropertyStatusRepositoryInterface $propertyStatusRepositoryInterface,
    ) {}

    public function index()
    {

        $user = Auth::user();

        $types = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->typeRepository, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with(['translations']);
            },
        )['data'];
        $types = collect($types)->map(function ($type) {
            return (new TypeResource($type))->toArray(request: request());
        })->toArray();

        $amenities = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->amenityRepository, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with(['translations']);
            },
        )['data'];
        $amenities = collect($amenities)->map(function ($amenity) {
            return (new AmenityResource($amenity))->toArray(request: request());
        })->toArray();

        $categories = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->categoryRepository, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {},
        )['data'];
        $categories = collect($categories)->map(function ($category) {
            return (new CategoryResource($category))->toArray(request: request());
        })->toArray();

        $floors = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->floorRepository, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with(['translations']);
            },
        )['data'];
        $floors = collect($floors)->map(function ($floor) {
            return (new FloorResource($floor))->toArray(request: request());
        })->toArray();

        $conditions = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->propertyConditionRepositoryInterface, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with(['translations']);
            },
        )['data'];
        $conditions = collect($conditions)->map(function ($cond) {
            return (new PropertyConditionResource($cond))->toArray(request: request());
        })->toArray();

        $cities = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->cityRepositoryInterface, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query
                    ->withCount(['properties'])
                    ->with(['translations']);
            },
        )['data'];
        $cities = collect($cities)->map(function ($orientation) {
            return (new CityResource($orientation))->toArray(request: request());
        })->toArray();

        $orientations = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->orientationRepositoryInterface, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with(['translations']);
            },
        )['data'];
        $orientations = collect($orientations)->map(function ($orientation) {
            return (new OrientationResource($orientation))->toArray(request: request());
        })->toArray();

        $contractTypes = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->contractTypeRepositoryInterface, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with(['translations']);
            },
        )['data'];
        $contractTypes = collect($contractTypes)->map(function ($orientation) {
            return (new ContractTypesResource($orientation))->toArray(request: request());
        })->toArray();

        $owners = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->userRepositoryInterface, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            beforeOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) use ($user) {
                $query->join('property', 'property.user_id', '=', 'users.id');
                $query->groupBy('users.id');
            },
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->selectRaw('users.*');
            },
        )['data'];
        $owners = collect($owners)->map(function ($owner) {
            return (new UserResource($owner))->toArray(request: request());
        })->toArray();


        $propertyStatus = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->propertyStatusRepositoryInterface, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with(['translations']);
            },
        )['data'];
        $propertyStatus = collect($propertyStatus)->map(function ($status) {
            return (new PropertyStatusResource($status))->toArray(request: request());
        })->toArray();


        $breadcrumbs = [
            ['name' => __('dashboard/layouts.aside.dashboard'), 'url' => route('dashboard.home')],
            ['name' => __('dashboard/layouts.aside.properties'), 'url' => null],
        ];

        return view('dashboard.pages.property.list', [
            'breadcrumbs' => $breadcrumbs,
            'categories' => $categories,
            'types' => $types,
            'amenities' => $amenities,
            'floors' => $floors,
            'conditions' => $conditions,
            'orientations' => $orientations,
            'owners' => $owners,
            'cities' => $cities,
            'contractTypes' => $contractTypes,
            'user' => $user,
            'propertyStatus' => $propertyStatus,
        ]);
    }


    public function blacklist()
    {

        $user = Auth::user();

        $breadcrumbs = [
            ['name' => __('dashboard/layouts.aside.dashboard'), 'url' => route('dashboard.home')],
            ['name' => __('dashboard/layouts.aside.properties'), 'url' => null],
        ];

        return view('dashboard.pages.property.blacklist', [
            'breadcrumbs' => $breadcrumbs,
            'user' => $user,
        ]);
    }

    public function favorite()
    {

        $user = Auth::user();

        $breadcrumbs = [
            ['name' => __('dashboard/layouts.aside.dashboard'), 'url' => route('dashboard.home')],
            ['name' => __('dashboard/layouts.aside.properties'), 'url' => null],
        ];

        return view('dashboard.pages.property.favorite', [
            'breadcrumbs' => $breadcrumbs,
            'user' => $user,
        ]);
    }

    // public function search(Request $request)
    // {
    //     $propertiesRes = AutoFIlterAndSortService::dynamicSearchFromRequest(
    //         getFunction: [$this->propertyRepositoryInterface, 'getList'],
    //         beforeOperation: function (\Illuminate\Database\Eloquent\Builder &$query, array $option) {
    //             $user = Auth::user();

    //             $query->leftJoin('user_hidden_list', function ($join) use ($user) {
    //                 $join->on('user_hidden_list.property_id', 'property.id')
    //                     ->where('user_hidden_list.user_id', $user?->id);
    //             });
    //             $query->addSelect(
    //                 'property.*',
    //                 DB::raw('(user_hidden_list.id IS NOT NULL) as is_blacklist')
    //             );
    //         },
    //         extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
    //             $user = Auth::user();

    //             // $query->select('property.*');

    //             if (!$user->isAdmin()) {
    //                 $query->where('property.user_id', $user->id);
    //             }

    //             $filterKeys = $option['filterKeys'];


    //             if (collect($filterKeys)->has('amenities')) {
    //                 $amenityIds = json_decode($filterKeys['amenities'][0]->value);
    //                 if (count($amenityIds) > 0) {
    //                     foreach ($amenityIds as $id) {
    //                         $pattern = "(^|,)$id(,|$)";
    //                         $query->where('cached_amenities_ids', 'REGEXP', $pattern);
    //                     }
    //                     // $patterns = array_map(fn($id) => "(^|,)$id(,|$)", $amenityIds);
    //                     // $regex = implode('|', $patterns);
    //                     // $query->where('cached_amenities_ids', 'REGEXP', $regex);
    //                 }
    //             }


    //             if ($filterKeys->has('is_blacklist')) {
    //                 $value = $filterKeys->get('is_blacklist')[0]->value;

    //                 if ($value == true) {
    //                     $query->whereNotNull('user_hidden_list.id');
    //                 } else {
    //                     $query->whereNull('user_hidden_list.id');
    //                 }
    //             } else {
    //                 $query->whereNull('user_hidden_list.id');
    //             }


    //             $query->with([
    //                 'owner',
    //                 'status.translations',
    //                 'translations',
    //                 'type.translations',
    //                 'city.translations',
    //                 'category.translations',
    //             ]);
    //         },
    //     );
    //     $data = collect($propertiesRes['data'])->map(function ($property) {
    //         return (new PropertyResource($property))->withFields(request()->get('fields'));
    //     })->toArray();

    //     return successResponse('', data: $data, pagination: $propertiesRes['pagination']);
    // }

    public function search(Request $request)
    {
        $propertiesRes = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->propertyRepositoryInterface, 'getList'],
            beforeOperation: function (\Illuminate\Database\Eloquent\Builder &$query, array $option) {
                $user = Auth::user();

                $query->leftJoin('user_hidden_list', function ($join) use ($user) {
                    $join->on('user_hidden_list.property_id', 'property.id')
                        ->where('user_hidden_list.user_id', $user?->id);
                });


                $query->leftJoin('favorite', function ($join) use ($user) {
                    $join->on('favorite.property_id', 'property.id')
                        ->where('favorite.user_id', $user?->id);
                });

                // This is good, it makes the 'is_blacklist' value available in the response.
                $query->addSelect(
                    'property.*',
                    DB::raw('user_hidden_list.id IS NOT NULL as is_blacklist'),
                    DB::raw('favorite.id IS NOT NULL as is_favorite_dynamic')
                );
            },
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {

                $user = Auth::user();
                $filterKeys = $option['filterKeys'];

                if (!$user->isAdmin()) {
                    $query->where('property.user_id', $user->id);
                }

                if (collect($filterKeys)->has('amenities')) {
                    $amenityIds = json_decode($filterKeys['amenities'][0]->value);
                    if (count($amenityIds) > 0) {
                        foreach ($amenityIds as $id) {
                            $pattern = "(^|,)$id(,|$)";
                            $query->where('cached_amenities_ids', 'REGEXP', $pattern);
                        }
                        // $patterns = array_map(fn($id) => "(^|,)$id(,|$)", $amenityIds);
                        // $regex = implode('|', $patterns);
                        // $query->where('cached_amenities_ids', 'REGEXP', $regex);
                    }
                }


                if ($filterKeys->has('is_blacklist')) {
                    /** @var ColumnFilterData $blacklistFilter */
                    $blacklistFilter = $filterKeys->get('is_blacklist')[0];

                    // Now you can react to different filter functions
                    switch ($blacklistFilter->filterFns) {
                        case FilterFnsEnum::equals:
                            if ($blacklistFilter->value == true) {
                                // Show ONLY blacklisted
                                $query->whereNotNull('user_hidden_list.id');
                            } else {
                                // Show ONLY non-blacklisted
                                $query->whereNull('user_hidden_list.id');
                            }
                            break;

                        case FilterFnsEnum::notEquals:
                            if ($blacklistFilter->value == true) {
                                // Show ONLY non-blacklisted
                                $query->whereNull('user_hidden_list.id');
                            } else {
                                // Show ONLY blacklisted
                                $query->whereNotNull('user_hidden_list.id');
                            }
                            break;

                        // You can add more cases here if needed
                        default:
                            // Default behavior if no specific function is handled
                            $query->whereNull('user_hidden_list.id');
                            break;
                    }
                } else {
                    // Default behavior: HIDE blacklisted items if the filter is not sent at all
                    // $query->whereNull('user_hidden_list.id');
                }


                if ($filterKeys->has('is_favorite')) {
                    /** @var ColumnFilterData $favoriteFilter */
                    $favoriteFilter = $filterKeys->get('is_favorite')[0];

                    switch ($favoriteFilter->filterFns) {
                        case FilterFnsEnum::equals:
                            if ($favoriteFilter->value == true) {
                                // Show ONLY favorite properties
                                $query->whereNotNull('favorite.id');
                            } else {
                                // Show ONLY non-favorite properties
                                $query->whereNull('favorite.id');
                            }
                            break;

                        case FilterFnsEnum::notEquals:
                            if ($favoriteFilter->value == true) {
                                // Show ONLY non-favorite properties
                                $query->whereNull('favorite.id');
                            } else {
                                // Show ONLY favorite properties
                                $query->whereNotNull('favorite.id');
                            }
                            break;

                        default:
                            // By default, we don't filter by favorite status
                            // unless explicitly asked. So we do nothing here.
                            break;
                    }
                }


                $query->with([
                    'owner',
                    'status.translations',
                    'translations',
                    'type.translations',
                    'city.translations',
                    'category.translations',
                ]);
            },
        );
        $data = collect($propertiesRes['data'])->map(function ($property) {
            return (new PropertyResource($property))->withFields(request()->get('fields'));
        })->toArray();

        return successResponse('', data: $data, pagination: $propertiesRes['pagination']);
    }

    public function create(Request $request)
    {
        $types = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->typeRepository, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with(['translations']);
            },
        )['data'];
        $types = collect($types)->map(function ($type) {
            return (new TypeResource($type))->toArray(request: request());
        })->toArray();

        $amenities = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->amenityRepository, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with(['translations']);
            },
        )['data'];
        $amenities = collect($amenities)->map(function ($amenity) {
            return (new AmenityResource($amenity))->toArray(request: request());
        })->toArray();

        $categories = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->categoryRepository, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {},
        )['data'];
        $categories = collect($categories)->map(function ($category) {
            return (new CategoryResource($category))->toArray(request: request());
        })->toArray();

        $floors = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->floorRepository, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with(['translations']);
            },
        )['data'];
        $floors = collect($floors)->map(function ($floor) {
            return (new FloorResource($floor))->toArray(request: request());
        })->toArray();

        $conditions = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->propertyConditionRepositoryInterface, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with(['translations']);
            },
        )['data'];
        $conditions = collect($conditions)->map(function ($cond) {
            return (new PropertyConditionResource($cond))->toArray(request: request());
        })->toArray();

        $cities = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->cityRepositoryInterface, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query
                    ->withCount(['properties'])
                    ->with(['translations']);
            },
        )['data'];
        $cities = collect($cities)->map(function ($orientation) {
            return (new CityResource($orientation))->toArray(request: request());
        })->toArray();

        $orientations = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->orientationRepositoryInterface, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with(['translations']);
            },
        )['data'];
        $orientations = collect($orientations)->map(function ($orientation) {
            return (new OrientationResource($orientation))->toArray(request: request());
        })->toArray();

        $contractTypes = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->contractTypeRepositoryInterface, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with(['translations']);
            },
        )['data'];
        $contractTypes = collect($contractTypes)->map(function ($orientation) {
            return (new ContractTypesResource($orientation))->toArray(request: request());
        })->toArray();

        $langs = Lang::active()->get();
        $user = Auth::user();

        $breadcrumbs = [
            ['name' => __('dashboard/layouts.aside.dashboard'), 'url' => route('dashboard.home')],
            ['name' => __('dashboard/layouts.aside.properties'), 'url' => route('dashboard.properties.index')],
        ];

        return view('dashboard.pages.property.create', [
            'categories' => $categories,
            'types' => $types,
            'amenities' => $amenities,
            'floors' => $floors,
            'conditions' => $conditions,
            'orientations' => $orientations,
            'cities' => $cities,
            'contractTypes' => $contractTypes,
            'langs' => $langs,
            'user' => $user,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    public function edit(Request $request, string $locale, Property $property)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && $property->user_id != $user->id) {
            abort(403, __('errors.403.title'));
        }

        $types = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->typeRepository, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with(['translations']);
            },
        )['data'];
        $types = collect($types)->map(function ($type) {
            return (new TypeResource($type))->toArray(request: request());
        })->toArray();

        $amenities = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->amenityRepository, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with(['translations']);
            },
        )['data'];
        $amenities = collect($amenities)->map(function ($amenity) {
            return (new AmenityResource($amenity))->toArray(request: request());
        })->toArray();

        $categories = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->categoryRepository, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {},
        )['data'];
        $categories = collect($categories)->map(function ($category) {
            return (new CategoryResource($category))->toArray(request: request());
        })->toArray();

        $floors = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->floorRepository, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with(['translations']);
            },
        )['data'];
        $floors = collect($floors)->map(function ($floor) {
            return (new FloorResource($floor))->toArray(request: request());
        })->toArray();

        $conditions = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->propertyConditionRepositoryInterface, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with(['translations']);
            },
        )['data'];
        $conditions = collect($conditions)->map(function ($cond) {
            return (new PropertyConditionResource($cond))->toArray(request: request());
        })->toArray();

        $cities = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->cityRepositoryInterface, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query
                    ->withCount(['properties'])
                    ->with(['translations']);
            },
        )['data'];
        $cities = collect($cities)->map(function ($orientation) {
            return (new CityResource($orientation))->toArray(request: request());
        })->toArray();

        $orientations = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->orientationRepositoryInterface, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with(['translations']);
            },
        )['data'];
        $orientations = collect($orientations)->map(function ($orientation) {
            return (new OrientationResource($orientation))->toArray(request: request());
        })->toArray();

        $contractTypes = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->contractTypeRepositoryInterface, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with(['translations']);
            },
        )['data'];
        $contractTypes = collect($contractTypes)->map(function ($orientation) {
            return (new ContractTypesResource($orientation))->toArray(request: request());
        })->toArray();

        $langs = Lang::active()->get();
        $user = Auth::user();

        // $property->load('translations');
        $property->load([
            'translations',
            'category',
            'orientation',
            'floor',
            'owner',
            'amenities',
            'availableTimes',
            'propertyAttribute.customAttribute',
            'favorite',
            'contractType',
            'type',
            'condition',
            'faqs',
            'images'
        ]);
        // return response()->json($property);

        $uploadImageUrl = route('api.dashboard-properties.images.store', [
            'property' => $property->id,
        ]);

        $breadcrumbs = [
            ['name' => __('dashboard/layouts.aside.dashboard'), 'url' => route('dashboard.home')],
            ['name' => __('dashboard/layouts.aside.properties'), 'url' => route('dashboard.properties.index')],
        ];

        return view('dashboard.pages.property.edit', [
            'categories' => $categories,
            'types' => $types,
            'amenities' => $amenities,
            'floors' => $floors,
            'conditions' => $conditions,
            'orientations' => $orientations,
            'cities' => $cities,
            'contractTypes' => $contractTypes,
            'langs' => $langs,
            'user' => $user,
            'property' => $property,
            'uploadImageUrl' => $uploadImageUrl,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    public function store(Store $request)
    {
        $validated = $request->validated();

        $property = $this->propertyRepositoryInterface->store($validated);


        // $interestedUserIds = Favorite::query()
        //     ->join('properties as p', 'favorites.property_id', '=', 'p.id')
        //     ->where(function ($q) use ($property) {
        //         $q->where('p.city_id', $property->city_id)
        //             ->orWhere('p.category_id', $property->category_id)
        //             ->orWhere('p.type_id', $property->type_id);
        //     })
        //     ->pluck('favorites.user_id')
        //     ->unique();

        $property->load([
            'images',
            'owner',
            'status.translations',
            'translations',
            'type.translations',
            'city.translations',
            'category.translations',
        ]);

        $interestedUserIds = Favorite::query()
            ->join('property as p', 'favorite.property_id', '=', 'p.id')
            ->select('favorite.user_id')
            ->where(function ($q) use ($property) {
                $q->where('p.city_id', $property->city_id)
                    ->orWhere('p.category_id', $property->category_id)
                    ->orWhere('p.type_id', $property->type_id)
                    ->orWhere('p.rooms_count', $property->rooms_count)
                    ->orWhere('p.year_built', $property->year_built)
                    ->orWhere('p.floor_id', $property->floor_id)
                    ->orWhere('p.orientation_id', $property->orientation_id)
                    ->orWhere('p.condition_id', $property->condition_id)
                    ->orWhere('p.city_id', $property->city_id);
            })
            ->groupBy('favorite.user_id')
            ->orderByRaw('COUNT(*) DESC')
            ->pluck('favorite.user_id');

        $interestedUsers = User::whereIn('id', $interestedUserIds)->get();
        foreach ($interestedUsers as $user) {
            if ($user->email) {
                if ($user->lang) {
                    App::setLocale($user->lang);
                }
                Mail::to($user->email)->queue(new NewPropertyNotification($property));
            }
        }

        return  successResponse(
            message: translate('messages.property.added_successfully'),
            data: $property
        );
    }

    public function update(Update $request, Property $property)
    {

        $validated = $request->validated();

        $oldData = $validated['old_data'];

        $oldStatus = $oldData['status_id'];
        $newStatus = $validated['status_id'] ?? $oldStatus;

        foreach (($request->deleted_images) ?? [] as $value) {
            $this->propertyRepositoryInterface->proceedImageDeleteByName($property->id, $value);
            PropertyGallery::where('property_id', $property->id)->where('name', $value)->delete();
        }

        $itemModel = $this->propertyRepositoryInterface->update($property, $validated);

        if ($oldStatus !== $newStatus && ($oldStatus === 'rejected' || $newStatus === 'rejected')) {
            $user = $property->load('owner')->owner;


            if ($user && $user->email) {

                if ($user->lang) {
                    App::setLocale($user->lang);
                }

                $oldStatus = PropertyStatus::find($oldStatus)->name;
                $newStatus = PropertyStatus::find($newStatus)->name;
                Mail::to([$user->email, $user->name])->queue(new PropertyRejectedStatusChanged($property, $oldStatus, $newStatus));
                // Mail::to($user->email)->send(new PropertyRejectedStatusChanged($property, $oldStatus, $newStatus));
            }
        }


        return  successResponse(
            message: translate('messages.property.updated_successfully'),
            data: $itemModel,
        );
    }

    public function uploadImages(Upload $request,  Property $property)
    {
        $property_id = $property->id;
        $maxImages = 10;
        if (($property->images()->count() + count($request->file('images'))) > $maxImages) {
            return response()->json(['message' => __('messages.property.max_images_exceeded', ['max' => $maxImages])], 422);
        }
        $uploadedImagePaths = [];
        foreach ($request->file('images') as $file) {
            try {
                $fileName = $this->propertyRepositoryInterface->proceedImage(file: $file, propertyId: $property_id);
                $property->images()->create([
                    'name' => $fileName,
                ]);
                $uploadedImagePaths[] = asset('/' . PROPERTY_IMAGE_NAME . "/$property_id/images/$fileName");
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Image upload failed for property {$property->id}: " . $e->getMessage());
                return errorResponse(
                    message: __('messages.property.error_saving_images')
                );
            }
        }
        return successResponse(
            message: trans_choice('messages.property.images_uploaded', count($uploadedImagePaths), ['count' => count($uploadedImagePaths)]),
            data: [
                'image_urls' => $uploadedImagePaths
            ],
            stateCode: 201
        );
    }

    public function destroy(Destroy $request, Property $property)
    {
        $this->propertyRepositoryInterface->destroy($property);
        return  successResponse(
            message: translate('messages.property.deleted_successfully'),
        );
    }
}
