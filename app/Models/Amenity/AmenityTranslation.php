<?php

namespace App\Models\Amenity;

use App\Models\GeneralModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * Class AmenityTranslation
 *
 * @property int $id Primary
 * @property mixed $name
 * @property mixed $locale
 * @property int $amenity_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @package App\Models
 */
class AmenityTranslation extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "amenities_translations";

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

    /**
     * Get the amenity associated with the AmenityTranslation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function amenity(): BelongsTo
    {
        return $this->belongsTo(Amenity::class, foreignKey: 'amenity_id', ownerKey: 'id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {});
    }
}
