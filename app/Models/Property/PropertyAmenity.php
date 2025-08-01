<?php

namespace App\Models\Property;

use App\Models\Amenity\Amenity;
use App\Models\GeneralModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * Class PropertyAmenity
 *
 * @property int $id Primary
 * @property int $property_id
 * @property int $amenity_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @package App\Models
 */
class PropertyAmenity extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "property_amenities";

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = ['id'];


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'publish_at' => 'datetime',
        ];
    }

    /**
     * Get the property associated with the PropertyKeyword
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, foreignKey: 'property_id', ownerKey: 'id');
    }

    /**
     * Get the amenity associated with the PropertyKeyword
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function amenity(): BelongsTo
    {
        return $this->belongsTo(Amenity::class, foreignKey: 'amenity_id', ownerKey: 'id');
    }
}
