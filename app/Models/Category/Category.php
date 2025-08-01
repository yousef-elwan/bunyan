<?php

namespace App\Models\Category;

use App\Models\GeneralModel;
use App\Models\Property\Property;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


/**
 * Class Category
 *
 * @property int $id Primary
 * @property mixed $image
 * @property \Carbon\Carbon $created_at
 * @property int $created_by
 * @property \Carbon\Carbon $updated_at
 * @property int $updated_by
 *
 * @package App\Models
 */
class Category extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "categories";

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = ['id'];


    /**
     * {@inheritdoc}
     * Defines the whitelist of columns that can be filtered by the front-end.
     * This includes columns from the main table and the translation table.
     */
    public function getFilterableColumns(): array
    {
        return [
            // Columns from the  table
            'id',
            'image',

            // --- From 'translations' table ---
            'name',
        ];
    }

    /**
     * {@inheritdoc}
     * Defines the whitelist of columns that can be used for sorting.
     */
    public function getSortableColumns(): array
    {
        return [
            // Columns from the  table
            'id',

            // --- From 'translations' table ---
            'name',
        ];
    }

    /**
     * {@inheritdoc}
     * Defines the columns from the 'translations' table to be included in the global search.
     */
    public function getSearchableTranslationColumns(): array
    {
        return [
            'name',
        ];
    }

    /**
     * {@inheritdoc}
     * This method is required by the interface to specify the translation table name.
     */
    public function getTranslationTable(): ?string
    {
        // Provide the exact name of your translation table.
        return 'categories_translations';
    }

    /**
     * {@inheritdoc}
     * This method is required by the interface to specify the foreign key in the translation table.
     */
    public function getTranslationForeignKey(): ?string
    {
        // This is the column in 'translations' that links back to the 'type' table.
        return 'category_id';
    }


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

    protected $appends = [
        'name',
        'image_url'
    ];


    // Preloading current locale translation at all times
    protected $with = [
        'defaultTranslation'
    ];

    public function name(): Attribute
    {

        return new Attribute(
            get: fn() => $this->defaultTranslation?->name,
        );
    }


    /**
     * Get all of the translations for the Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations(): HasMany
    {
        return $this->hasMany(CategoryTranslation::class, foreignKey: 'category_id', localKey: 'id');
    }


    /**
     * Get the defaultTranslation associated with the Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function defaultTranslation(): HasOne
    {
        return $this->hasOne(CategoryTranslation::class, 'category_id')
            // This is a modern way to create a "has one" that falls back.
            // It tries to get the current locale, if not found, it gets the first one.
            ->ofMany([], function ($query) {
                $query->where('locale', app()->getLocale());
            });
    }


    /**
     * Get all of the properties for the Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'category_id', 'id');
    }


    public function imageUrl(): Attribute
    {
        $image = $this->image;

        $value = !is_null($image) ?  asset(CATEGORY_IMAGE_NAME . '/' . $image) :
            config('app.web_config.default_category_image');

        return new Attribute(
            get: fn() => $value,
        );
    }



    protected static function boot()
    {
        parent::boot();
    }
}
