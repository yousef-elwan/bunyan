<?php

namespace App\Models;

use App\Models\Property\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * Class Favorite
 *
 * @property int $id Primary
 * @property int $user_id
 * @property int $property_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @package App\Models
 */
class Favorite extends GeneralModel
{

    protected $table = "favorite";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'property_id',
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
            'user_id',
            'property_id',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
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
            'user_id',
            'property_id',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
        ];
    }

    public function wishlistProperty(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id')->active();
    }
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
