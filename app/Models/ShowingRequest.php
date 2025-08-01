<?php

namespace App\Models;

use App\Models\Property\Property;
use App\Models\Property\PropertyAvailableTime;
use App\Models\ShowingRequestType\ShowingRequestType;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * Class ShowingRequest
 *
 * @property int $id Primary
 * @property int $owner_id
 * @property int $user_id
 * @property int $property_id
 * @property int $showing_request_type_id
 * @property int $time_id
 * @property \Carbon\Carbon $time
 * @property mixed $email
 * @property mixed $NAME
 * @property mixed $mobile
 * @property string $massage
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class ShowingRequest extends GeneralModel
{

    protected $table = "showing_request";
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [
        'id'
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'property_id' => 'integer',
    ];



    /**
     * {@inheritdoc}
     * Defines the whitelist of columns that can be filtered by the front-end.
     * This includes columns from the main table and the translation table.
     */
    public function getFilterableColumns(): array
    {
        return [
            'id',
            'owner_id',
            'user_id',
            'property_id',
            'showing_request_type_id',
            'time_id',
            'time',
            'email',
            'NAME',
            'mobile',
            'massage',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * {@inheritdoc}
     * Defines the whitelist of columns that can be used for sorting.
     */
    public function getSortableColumns(): array
    {
        return [
            'id',
            'owner_id',
            'user_id',
            'property_id',
            'showing_request_type_id',
            'time_id',
            'time',
            'email',
            'NAME',
            'mobile',
            'massage',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * {@inheritdoc}
     * Defines the columns from the main table to be included in the global search.
     */
    public function getSearchableColumns(): array
    {
        return [
            'email',
            'mobile',
            'massage',
        ];
    }




    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
    public function availableTime(): BelongsTo
    {
        return $this->belongsTo(PropertyAvailableTime::class, 'time_id');
    }
    public function showingRequestType(): BelongsTo
    {
        return $this->belongsTo(ShowingRequestType::class, foreignKey: 'showing_request_type_id', ownerKey: 'id');
    }
}
