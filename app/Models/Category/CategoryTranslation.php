<?php

namespace App\Models\Category;

use App\Models\GeneralModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * Class CategoryTranslation
 *
 * @property int $id Primary
 * @property mixed $name
 * @property mixed $locale
 * @property int $category_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @package App\Models
 */
class CategoryTranslation extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "categories_translations";

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
     * Get the category associated with the CategoryTranslation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, foreignKey: 'category_id', ownerKey: 'id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {});
    }
}
