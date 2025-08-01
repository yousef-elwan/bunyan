<?php

namespace App\Models\Property;

use App\Models\GeneralModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * Class PropertyTranslation
 *
 * @property int $id Primary
 * @property mixed $location
 * @property string $content
 * @property mixed $locale
 * @property int $property_id
 * @property mixed $slug
 * @property mixed $meta_title
 * @property string $meta_description
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @package App\Models
 */
class PropertyTranslation extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "property_translations";

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = ['id'];

    protected $with = ['attributes'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
    /**
     * Get the property associated with the PropertyTranslation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, foreignKey: 'property_id', ownerKey: 'id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(PropertyTranslation::class, foreignKey: 'property_id', localKey: 'id');
    }


    public function attributes(): HasMany
    {
        return $this->hasMany(PropertyAttributeValue::class, foreignKey: 'property_id', localKey: 'property_id');
    }


    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            if (!isset($model->content)) {
                $model->content = "";
            }
            if (!isset($model->slug)) {
                $model->slug = getSlug(title: $model->title, language: $model->locale);
            }
            if (!isset($model->meta_title)) {
                $model->meta_title = "";
            }
            if (!isset($model->meta_description)) {
                $model->meta_description = "";
            }
        });
    }
}
