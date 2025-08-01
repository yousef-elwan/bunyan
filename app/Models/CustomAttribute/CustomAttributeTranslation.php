<?php

namespace App\Models\CustomAttribute;

use App\Models\GeneralModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * Class CustomAttributeTranslation
 *
 * @property int $id Primary
 * @property mixed $name
 * @property mixed $locale
 * @property int $attribute_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @package App\Models
 */
class CustomAttributeTranslation extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "attributes_translations";

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
        ];
    }


    public function customAttribute(): BelongsTo
    {
        return $this->belongsTo(CustomAttribute::class, foreignKey: 'attribute_id', ownerKey: 'id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {});
    }
}
