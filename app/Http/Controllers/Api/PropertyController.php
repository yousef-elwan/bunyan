<?php

namespace App\Http\Controllers\Api;

use App\Enums\FilterFnsEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\Web\PropertyResource;
use App\Models\Property\Property;
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
use App\Services\AutoFIlterAndSortService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Report\Send as SendReport;
use App\Http\Requests\ShowingRequest\Send;
use App\Models\Favorite;
use App\Models\Property\PropertyReport;
use App\Models\User;
use App\Models\UserBlackList;
use App\Repositories\Contracts\ChatRepositoryInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PropertyController extends Controller
{

    public function __construct(
        protected ChatRepositoryInterface $chatRepositoryInterface,
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
    ) {}

    public function index(Request $request)
    {

        $user = getAuthUser();

        $propertiesRes = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->propertyRepositoryInterface, 'getList'],
            beforeOperation: function (\Illuminate\Database\Eloquent\Builder &$query, array $option) use ($user) {
                if ($user != 'offline') {

                    $query->leftJoin('user_hidden_list', function ($join) use ($user) {
                        $join->on('user_hidden_list.property_id', 'property.id')
                            ->where('user_hidden_list.user_id', $user?->id);
                    });

                    $query->leftJoin('favorite', function ($join) use ($user) {
                        $join->on('favorite.property_id', 'property.id')
                            ->where('favorite.user_id', $user?->id);
                    });

                    $query->addSelect(
                        'property.*',
                        DB::raw('user_hidden_list.id IS NOT NULL as is_blacklist'),
                        DB::raw('favorite.id IS NOT NULL as is_favorite_dynamic')
                    );
                }
            },
            extraOperation: function (\Illuminate\Database\Eloquent\Builder $query, array $option) use ($user) {

                
                $filterKeys = $option['filterKeys'];
                
                if ($user != 'offline') {


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
                }


                if ($filterKeys->has('amenities')) {
                    // The value is expected to be a JSON string of an array of IDs.
                    $amenityIds = json_decode($filterKeys->get('amenities')[0]->value);

                    if (is_array($amenityIds) && count($amenityIds) > 0) {
                        foreach ($amenityIds as $id) {
                            // REGEXP to find the ID in a comma-separated string like "1,5,12"
                            $pattern = "(^|,)" . intval($id) . "(,|$)";
                            $query->where('property.cached_amenities_ids', 'REGEXP', $pattern);
                        }
                    }
                }

                // Eager Loading for API Resource ---
                // This is for optimizing the final output, so it belongs here.
                $query->with([
                    'owner',
                    'status.translations',
                    'translations',
                    'type.translations',
                    'city.translations',
                    'category.translations',
                ]);
            }
        );

        $data = collect($propertiesRes['data'])->map(function ($property) {
            return (new PropertyResource($property))->withFields(request()->get('fields'));
        })->toArray();
        return successResponse('', data: $data, pagination: $propertiesRes['pagination']);
    }

    /**
     * Send a message to the property agent/owner.
     *
     * @param Property $property // Or just Request $request if property_id is in the body
     * @param Request $request   // Use a FormRequest for better validation
     * @return JsonResponse
     */
    public function contactAgent(Send $request,  Property $property): JsonResponse // Consider a FormRequest here
    {

        // $validatedData = $request->validated();

        // $recipient = $property->owner;
        // $sender = Auth::user();
        // $conversation = $this->findOrCreateConversation($sender, $recipient, $property);
        // $messageData = ['body' => $validatedData['message']];
        // $message = $this->sendMessage($conversation, $sender, $messageData);


        // $user = Auth::user(); // Will be null if guest
        // $validatedData = $request->validated(); // Get the validated data

        try {
            // $agent = $property->owner; // Assuming 'owner' relationship on Property model points to the agent/user
            // // if (!$agent || !$agent->email) {
            // //     return errorResponse(
            // //         message: trans('messages.contact.agent_not_found_or_no_email'),
            // //         state: 404
            // //     );
            // // }

            // $contactDetails = [
            //     'propertyName' => $property->title, // Or another identifying property field
            //     'propertyUrl' => route('properties.details', [
            //         'locale' => getDefaultLanguage(),
            //         'property' => $property->id,
            //     ]), // Generate URL to property
            //     'name' => $validatedData['name'],
            //     'email' => $validatedData['email'],
            //     'mobile' => $validatedData['mobile'],
            //     'message' => $validatedData['message'],
            //     'loggedInUserId' => $user ? $user->id : null,
            // ];

            // // Option 1: Send an Email
            // try {
            //     if (isset($agent->email)) {
            //         Mail::to($agent->email)->send(new AgentContactMail($contactDetails, $agent->name));
            //     }
            // } catch (\Throwable $th) {
            //     //throw $th;
            // }

            // // Option 2: Store in database (if you have an AgentMessage model)
            // Message::create([
            //     'property_id' => $property->id,
            //     'user_id' => $agent->id,
            //     'sender_user_id' => $user ? $user->id : null, // If logged in
            //     'name' => $validatedData['name'],
            //     'email' => $validatedData['email'],
            //     'mobile' => $validatedData['mobile'],
            //     'message' => $validatedData['message'],
            // ]);

            // return successResponse(
            //     message: trans('messages.contact.message_sent_success'),
            // );

            $this->chatRepositoryInterface->handlePropertyContact($property, $request->validated());

            return successResponse(
                message: trans('messages.contact.message_sent_success'),
            );
        } catch (Exception $e) {
            throw $e;
            report($e);
            return errorResponse(
                message: trans('messages.contact.message_sent_failed'),
                state: 500
            );
        }
    }


    // public function contactAgent(Send $request, Property $property): JsonResponse // Consider a FormRequest here
    // {

    //     $user = Auth::user(); // Will be null if guest
    //     $validatedData = $request->validated(); // Get the validated data

    //     try {
    //         $agent = $property->owner; // Assuming 'owner' relationship on Property model points to the agent/user
    //         // if (!$agent || !$agent->email) {
    //         //     return errorResponse(
    //         //         message: trans('messages.contact.agent_not_found_or_no_email'),
    //         //         state: 404
    //         //     );
    //         // }

    //         $contactDetails = [
    //             'propertyName' => $property->title, // Or another identifying property field
    //             'propertyUrl' => route('properties.details', [
    //                 'locale' => getDefaultLanguage(),
    //                 'property' => $property->id,
    //             ]), // Generate URL to property
    //             'name' => $validatedData['name'],
    //             'email' => $validatedData['email'],
    //             'mobile' => $validatedData['mobile'],
    //             'message' => $validatedData['message'],
    //             'loggedInUserId' => $user ? $user->id : null,
    //         ];

    //         // Option 1: Send an Email
    //         try {
    //             if (isset($agent->email)) {
    //                 Mail::to($agent->email)->send(new AgentContactMail($contactDetails, $agent->name));
    //             }
    //         } catch (\Throwable $th) {
    //             //throw $th;
    //         }

    //         // Option 2: Store in database (if you have an AgentMessage model)
    //         Message::create([
    //             'property_id' => $property->id,
    //             'user_id' => $agent->id,
    //             'sender_user_id' => $user ? $user->id : null, // If logged in
    //             'name' => $validatedData['name'],
    //             'email' => $validatedData['email'],
    //             'mobile' => $validatedData['mobile'],
    //             'message' => $validatedData['message'],
    //         ]);

    //         return successResponse(
    //             message: trans('messages.contact.message_sent_success'),
    //         );
    //     } catch (Exception $e) {
    //         throw $e;
    //         report($e);
    //         return errorResponse(
    //             message: trans('messages.contact.message_sent_failed'),
    //             state: 500
    //         );
    //     }
    // }

    /**
     * Submit a report for a property.
     *
     * @param Property $property
     * @param Request $request
     * @return JsonResponse
     */
    public function submitReport(SendReport $request, Property $property): JsonResponse
    {
        $user = Auth::user(); // Assumes authentication via auth:sanctum or web session


        try {
            PropertyReport::create([
                'user_id' => $user?->id ?? null,
                'property_id' => $property->id,
                'type_id' => $request->input('type_id'),
                'message' => $request->input('message'),
                'name' => isset($user) ? ($user->first_name . ' ' . $user->last_name) : $request->input('name'),
                'mobile' => $user->mobile ?? $request->input('mobile'),
                'email' => $user->email ?? $request->input('email'),
                // 'status' => 'pending', // Optional: if you have a review process
            ]);

            return successResponse(
                message: trans('messages.report.success_message'),
                data: [
                    'property_id' => $property->id
                ]
            );
        } catch (Exception $e) {
            report($e); // Log the exception

            return errorResponse(
                message: trans('messages.report.report_submit_failed'),
            );
        }
    }

    /**
     * Toggle the favorite status of a property for the authenticated user.
     *
     * @param Property $property Automatically resolved by Route Model Binding
     * @return JsonResponse
     */
    public function toggleFavorite(Property $property): JsonResponse
    {
        /** @var User $user auth user */
        $user = Auth::user();

        try {
            $isFavorited = $user->hasFavorited($property);

            if ($isFavorited) {
                // Remove from favorites
                $user->favorites()->where('property_id', $property->id)->delete();
                $message = trans('messages.favorite.remove_success');
                $newStatus = false;
            } else {
                // Add to favorites
                Favorite::create([
                    'user_id' => $user->id,
                    'property_id' => $property->id,
                ]);
                // Or: $user->favorites()->create(['property_id' => $property->id]);
                $message = trans('messages.favorite.add_success');
                $newStatus = true;
            }

            return successResponse(
                message: $message,
                data: [
                    'is_active' => $newStatus,
                    'property_id' => $property->id
                ]
            );
        } catch (Exception $e) {

            // Log the error for debugging
            report($e); // or Log::error($e->getMessage());

            return errorResponse(
                message: trans('messages.favorite.action_failed')
            );
        }
    }

    /**
     * Toggle the blacklist status of a property for the authenticated user.
     *
     * @param Property $property Automatically resolved by Route Model Binding
     * @return JsonResponse
     */
    public function toggleBlacklist(Property $property): JsonResponse
    {


        /** @var User $user auth user */
        $user = Auth::user();

        try {
            $isBlacklisted = $user->hasBlacklisted($property);

            if ($isBlacklisted) {
                // Remove from blacklist
                $user->blacklistedPropertiesRelation()->where('property_id', $property->id)->delete();
                $message = trans('messages.blacklist.remove_success');
                $newStatus = false;
            } else {
                // Add to blacklist
                UserBlackList::create([
                    'user_id' => $user->id,
                    'property_id' => $property->id,
                ]);
                // Or: $user->blacklistedPropertiesRelation()->create(['property_id' => $property->id]);
                $message = trans('messages.blacklist.add_success');
                $newStatus = true;
            }

            return successResponse(
                message: $message,
                data: [
                    'is_active' => $newStatus,
                    'property_id' => $property->id
                ]
            );
        } catch (Exception $e) {
            throw $e;
            report($e);
            return errorResponse(
                message: trans('messages.blacklist.action_failed')
            );
        }
    }
}
