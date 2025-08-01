<?php

namespace App\Models\CustomAttribute;

use App\Models\GeneralModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * Class CustomAttributeValue
 *
 * @property int $id Primary
 * @property int $attribute_id
 * @property int $attribute_translation_id
 * @property mixed $value
 * @property \Carbon\Carbon $created_at
 * @property int $created_by
 * @property \Carbon\Carbon $updated_at
 * @property int $updated_by
 *
 * @package App\Models
 */
class CustomAttributeValue extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "attributes_values";

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

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(CustomAttribute::class, foreignKey: 'attribute_id', ownerKey: 'id');
    }

    protected static function boot()
    {
        parent::boot();
    }
}
