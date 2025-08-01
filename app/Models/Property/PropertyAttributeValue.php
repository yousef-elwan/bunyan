<?php

namespace App\Models\Property;

use App\Models\CustomAttribute\CustomAttribute;
use App\Models\GeneralModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * Class PropertyAttributeValue
 *
 * @property int $id Primary
 * @property int $property_id
 * @property int $attribute_id
 * @property mixed $value
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @package App\Models
 */
class PropertyAttributeValue extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "property_attributes";

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


    public function customAttribute(): BelongsTo
    {
        return $this->belongsTo(CustomAttribute::class, foreignKey: 'attribute_id', ownerKey: 'id');
    }
}
