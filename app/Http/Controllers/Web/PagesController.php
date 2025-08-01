<?php

namespace App\Http\Controllers\Web;

use App\Data\ColumnFilterData;
use App\Data\ColumnSortData;
use App\Enums\FilterFnsEnum;
use App\Enums\PaginationFormateEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\Web\AmenityResource;
use App\Http\Resources\Web\CategoryResource;
use App\Http\Resources\Web\CityResource;
use App\Http\Resources\Web\ContractTypesResource;
use App\Http\Resources\Web\FloorResource;
use App\Http\Resources\Web\OrientationResource;
use App\Http\Resources\Web\PropertyConditionResource;
use App\Http\Resources\Web\PropertyResource;
use App\Http\Resources\Web\ReportTypeResource;
use App\Http\Resources\Web\SliderResource;
use App\Http\Resources\Web\TypeResource;
use App\Models\PrivacyPolicyTranslation;
use App\Models\Property\Property;
use App\Models\ShowingRequestType\ShowingRequestType;
use App\Models\TermOfServiceTranslation;
use App\Models\TermOfUseTranslation;
use App\Models\User;
use App\Repositories\Contracts\AmenityRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\CityRepositoryInterface;
use App\Repositories\Contracts\ContractTypeRepositoryInterface;
use App\Repositories\Contracts\FloorRepositoryInterface;
use App\Repositories\Contracts\OrientationRepositoryInterface;
use App\Repositories\Contracts\PropertyConditionRepositoryInterface;
use App\Repositories\Contracts\PropertyReportRepositoryInterface;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\Repositories\Contracts\ReportTypeRepositoryInterface;
use App\Repositories\Contracts\SliderRepositoryInterface;
use App\Repositories\Contracts\TypeRepositoryInterface;
use App\Repositories\Contracts\ShowingRequestRepositoryInterface;
use App\Services\AutoFIlterAndSortService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PagesController extends Controller
{

    public function __construct(
        protected TypeRepositoryInterface $typeRepository,
        protected AmenityRepositoryInterface $amenityRepository,
        protected CategoryRepositoryInterface $categoryRepository,
        protected FloorRepositoryInterface $floorRepository,
        protected PropertyConditionRepositoryInterface $propertyConditionRepository,
        protected OrientationRepositoryInterface $orientationRepositoryInterface,
        protected CityRepositoryInterface $cityRepositoryInterface,
        protected PropertyRepositoryInterface $propertyRepositoryInterface,
        protected PropertyReportRepositoryInterface $propertyReportRepositoryInterface,
        protected SliderRepositoryInterface $sliderRepositoryInterface,
        protected PropertyConditionRepositoryInterface $propertyConditionRepositoryInterface,
        protected ShowingRequestRepositoryInterface $showingRequestRepositoryInterface,
        protected ContractTypeRepositoryInterface $contractTypeRepositoryInterface,
        protected ReportTypeRepositoryInterface $reportTypeRepositoryInterface,
    ) {}

    /**
     * open home page.
     */
    function home(Request $request)
    {

        $sliders = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->sliderRepositoryInterface, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with(['translations']);
            },
        )['data'];
        $sliders = collect($sliders)->map(function ($type) {
            return (new SliderResource($type));
        })->toArray();

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
            return (new TypeResource($type));
        })->toArray();

        $categories = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->categoryRepository, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query
                    ->withCount(['properties'])
                    ->with([
                        'translations',
                        'properties' => function ($q) {
                            $q->orderByRaw('is_featured DESC, RAND()')
                                ->limit(6)
                                ->with([
                                    'translations',
                                    'type.translations',
                                    'city.translations'
                                ]);
                        },
                    ]);
            },
        )['data'];
        $categories = collect($categories)->map(function ($category) {
            return (new CategoryResource($category));
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
            return (new AmenityResource($amenity));
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
            return (new FloorResource($floor));
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
            return (new PropertyConditionResource($cond));
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
            return (new CityResource($orientation));
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
            return (new OrientationResource($orientation));
        })->toArray();

        // $newProperty = AutoFIlterAndSortService::dynamicSearchFromRequest(
        //     getFunction: [$this->propertyRepositoryInterface, 'getList'],
        //     filters: [
        //         new ColumnFilterData(
        //             id: 'is_new',
        //             value: true,
        //             filterFns: FilterFnsEnum::equals
        //         )
        //     ],
        //     sorting: [],
        //     globalFilter: "",
        //     paginationFormate: PaginationFormateEnum::none,
        //     extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
        //         $query->with([
        //             'translations',
        //             'type.translations',
        //             'city.translations'
        //         ]);
        //     },
        // )['data'];
        // $newProperty = collect($newProperty)->map(function ($orientation) {
        //     return (new PropertyResource($orientation));
        // })->toArray();

        $newProperty = AutoFIlterAndSortService::dynamicSearchFromRequest(
            page: "1",
            perPage: "20",
            getFunction: [$this->propertyRepositoryInterface, 'getList'],
            sorting: [
                new ColumnSortData(
                    id: 'created_at',
                    desc: true
                )
            ],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::separated,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {

                // التعديل الثالث: أضفنا limit لجلب 6 عقارات فقط
                $query->limit(6)
                    ->with([
                        'translations',
                        'type.translations',
                        'city.translations'
                    ]);
            },
        )['data'];

        $featuredProperty = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->propertyRepositoryInterface, 'getList'],
            filters: [
                new ColumnFilterData(
                    id: 'is_featured',
                    value: true,
                    filterFns: FilterFnsEnum::equals
                )
            ],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with([
                    'translations',
                    'type.translations',
                    'city.translations'
                ]);
            },
        )['data'];
        $featuredProperty = collect($featuredProperty)->map(function ($orientation) {
            return (new PropertyResource($orientation));
        })->toArray();

        return view('app.pages.home.page', [
            'categories' => $categories,
            'types' => $types,
            'amenities' => $amenities,
            'floors' => $floors,
            'conditions' => $conditions,
            'orientations' => $orientations,
            'cities' => $cities,
            // 'newProperty' => $newProperty,
            'owlProperties' =>  owlProperties,
            'sliders' => $sliders,
            // 'roomsCount' => $roomsCount,
            // 'properties' => $properties,
            'propertiesFeatures' => $featuredProperty,
            'propertiesNew' => $newProperty,
            // 'partners' => $partners,
        ]);
    }

    /**
     * open property details page.
     */
    function details(Request $request, string $locale, Property $property)
    {



        /** @var User $user auth user */
        $user = Auth::user();
        $isFavorited = $user?->hasFavorited($property) ?? false;

        $isBlackList = $user?->hasBlacklisted($property) ?? false;

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
            return (new TypeResource($type))->toArray(request());
        })->toArray();

        $reportTypes = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->reportTypeRepositoryInterface, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with(['translations']);
            },
        )['data'];
        $reportTypes = collect($reportTypes)->map(function ($type) {
            return (new ReportTypeResource($type))->toArray(request());
        })->toArray();

        $categories = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->categoryRepository, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query
                    ->withCount(['properties'])
                    ->with([
                        'translations',
                        'properties' => function ($q) {
                            $q->orderByRaw('is_featured DESC, RAND()')
                                ->limit(6)
                                ->with([
                                    'translations',
                                    'type.translations',
                                    'city.translations'
                                ]);
                        },
                    ]);
            },
        )['data'];
        $categories = collect($categories)->map(function ($category) {
            return (new CategoryResource($category))->toArray(request());
        })->toArray();

        $property['showing_request_types'] = ShowingRequestType::all();
        $property->load([
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
            'images',
            'currency.translations',
        ]);

        $property =  (new PropertyResource($property))->toArray(request());
        
        $property['is_favorite'] = $isFavorited;
        $property['is_blacklist'] = $isBlackList;

        return view('app.pages.property.details', compact('property', 'types', 'categories', 'reportTypes'));
    }

    /**
     * open privacy policy page.
     */
    function privacyPolicy()
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
            return (new TypeResource($type))->toArray(request());
        })->toArray();

        $categories = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->categoryRepository, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query
                    ->withCount(['properties'])
                    ->with([
                        'translations',
                        'properties' => function ($q) {
                            $q->orderByRaw('is_featured DESC, RAND()')
                                ->limit(6)
                                ->with([
                                    'translations',
                                    'type.translations',
                                    'city.translations'
                                ]);
                        },
                    ]);
            },
        )['data'];
        $categories = collect($categories)->map(function ($category) {
            return (new CategoryResource($category))->toArray(request());
        })->toArray();


        $locale = getDefaultLanguage();
        $legal = PrivacyPolicyTranslation::where('locale', $locale)->first() ?? PrivacyPolicyTranslation::first();
        if ($legal) {
            $data = $legal['updated_at'];
            $legal['updated_at_formate'] = Carbon::parse($data)->translatedFormat('j F Y');
        }
        return view('app.pages.privacy-policy.page', [
            'legal' => $legal,
            'types' => $types,
            'categories' => $categories,
        ]);
    }

    /**
     * open terms Of use page.
     */
    function termsOfUse()
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
            return (new TypeResource($type))->toArray(request());
        })->toArray();

        $categories = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->categoryRepository, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query
                    ->withCount(['properties'])
                    ->with([
                        'translations',
                        'properties' => function ($q) {
                            $q->orderByRaw('is_featured DESC, RAND()')
                                ->limit(6)
                                ->with([
                                    'translations',
                                    'type.translations',
                                    'city.translations'
                                ]);
                        },
                    ]);
            },
        )['data'];
        $categories = collect($categories)->map(function ($category) {
            return (new CategoryResource($category))->toArray(request());
        })->toArray();


        $locale = getDefaultLanguage();
        $legal = TermOfUseTranslation::where('locale', $locale)->first() ?? TermOfUseTranslation::first();
        if ($legal) {
            $data = $legal['updated_at'];
            $legal['updated_at_formate'] = Carbon::parse($data)->translatedFormat('j F Y');
        }
        return view('app.pages.terms-of-use.page', [
            'legal' => $legal,
            'types' => $types,
            'categories' => $categories,
        ]);
    }

    /**
     * open terms Of service page.
     */
    function termsOfService()
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
            return (new TypeResource($type))->toArray(request());
        })->toArray();

        $categories = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->categoryRepository, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query
                    ->withCount(['properties'])
                    ->with([
                        'translations',
                        'properties' => function ($q) {
                            $q->orderByRaw('is_featured DESC, RAND()')
                                ->limit(6)
                                ->with([
                                    'translations',
                                    'type.translations',
                                    'city.translations'
                                ]);
                        },
                    ]);
            },
        )['data'];
        $categories = collect($categories)->map(function ($category) {
            return (new CategoryResource($category))->toArray(request());
        })->toArray();

        $locale = getDefaultLanguage();
        $legal = TermOfServiceTranslation::where('locale', $locale)->first() ?? TermOfServiceTranslation::first();
        if ($legal) {
            $data = $legal['updated_at'];
            $legal['updated_at_formate'] = Carbon::parse($data)->translatedFormat('j F Y');
        }
        return view('app.pages.terms-of-service.page', [
            'legal' => $legal,
            'types' => $types,
            'categories' => $categories,
        ]);
    }

    /**
     * open contact us page.
     */
    function contactUs()
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
            return (new TypeResource($type))->toArray(request());
        })->toArray();

        $categories = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->categoryRepository, 'getList'],
            filters: [],
            sorting: [],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query
                    ->withCount(['properties'])
                    ->with([
                        'translations',
                        'properties' => function ($q) {
                            $q->orderByRaw('is_featured DESC, RAND()')
                                ->limit(6)
                                ->with([
                                    'translations',
                                    'type.translations',
                                    'city.translations'
                                ]);
                        },
                    ]);
            },
        )['data'];
        $categories = collect($categories)->map(function ($category) {
            return (new CategoryResource($category))->toArray(request());
        })->toArray();
        return view('app.pages.contact-us.page', compact('types', 'categories'));
    }

    /**
     * open search page.
     */
    function search(Request $request)
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
            return (new TypeResource($type))->toArray(request());
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
            return (new AmenityResource($amenity))->toArray(request());
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
            return (new CategoryResource($category))->toArray(request());
        })->toArray();

        $floors = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->floorRepository, 'getList'],
            filters: [],
            sorting: [
                new ColumnSortData(
                    id: 'value',
                    desc: false
                )
            ],
            globalFilter: "",
            paginationFormate: PaginationFormateEnum::none,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with(['translations']);
            },
        )['data'];
        $floors = collect($floors)->map(function ($floor) {
            return (new FloorResource($floor))->toArray(request());
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
            return (new PropertyConditionResource($cond))->toArray(request());
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
            return (new CityResource($orientation))->toArray(request());
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
            return (new OrientationResource($orientation))->toArray(request());
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
            return (new ContractTypesResource($orientation))->toArray(request());
        })->toArray();

        return view('app.pages.search.page', [
            'categories' => $categories,
            'types' => $types,
            'amenities' => $amenities,
            'floors' => $floors,
            'conditions' => $conditions,
            'orientations' => $orientations,
            'governorates' => $cities,
            'contractTypes' => $contractTypes,
            'roomsCount' => [1, 2, 3, 4, 5],
        ]);
    }

    /**
     * open login page.
     */
    function login()
    {
        return view('app.auth.login');
    }

    /**
     * open register page.
     */
    function register()
    {
        return view('app.auth.register');
    }
}
