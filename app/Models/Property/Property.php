<?php

namespace App\Models\Property;

use App\Models\Category\Category;
use App\Models\City\City;
use App\Models\ContractType\ContractType;
use App\Models\Currency\Currency;
use App\Models\Favorite;
use App\Models\Floor\Floor;
use App\Models\GeneralModel;
use App\Models\Orientation\Orientation;
use App\Models\PropertyCondition\PropertyCondition;
use App\Models\PropertyFAQ\PropertyFAQ;
use App\Models\PropertyStatus\PropertyStatus;
use App\Models\Type\Type;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Amenity\Amenity;
use Carbon\Carbon;

/**
 * Class Property
 *
 * @property int $id Primary
 * @property int $user_id
 * @property float $latitude
 * @property float $longitude
 * @property mixed $video_url
 * @property float $price
 * @property mixed $size
 * @property mixed $rooms_count
 * @property mixed $view_count
 * @property mixed $year_built
 * @property \Carbon\Carbon $available_from
 * @property mixed $price_on_request
 * @property mixed $is_new
 * @property mixed $is_featured
 * @property mixed $is_rejected
 * @property string $reject_cause
 * @property int $contract_type_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $floor_id
 * @property mixed $status_id
 * @property mixed $currency_id
 * @property int $category_id
 * @property int $orientation_id
 * @property int $type_id
 * @property int $created_by
 * @property int $condition_id
 * @property int $city_id
 * @property mixed $cached_amenities_ids
 * @property mixed $cached_floor_value
 * @property \Carbon\Carbon $published_at
 * @property int $updated_by
 *
 * @package App\Models
 */
class Property extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "property";

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
            'is_new' => 'boolean',
            'is_featured' => 'boolean',
            'price_on_request' => 'boolean',
            'view_count' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'publish_at' => 'datetime',
        ];
    }

    protected $appends = [
        'image_url',
        'location',
        'slug',
        'meta_title',
        'meta_description',
        'content',
        'meta_keywords',
        'price_display',
        'published_at_formatted',
    ];


    // Preloading current locale translation at all times
    protected $with = [
        'defaultTranslation',
        'currency',
    ];


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
            'user_id',
            'latitude',
            'longitude',
            'video_url',
            'price',
            'size',
            'rooms_count',
            'view_count',
            'year_built',
            'available_from',
            'price_on_request',
            'is_new',
            'is_featured',
            'is_rejected',
            'reject_cause',
            'contract_type_id',
            'created_at',
            'updated_at',
            'floor_id',
            'status_id',
            'currency_id',
            'category_id',
            'orientation_id',
            'type_id',
            'created_by',
            'condition_id',
            'city_id',
            'cached_amenities_ids',
            'cached_floor_value',
            'published_at',
            'updated_by',

            // --- From 'translations' table ---
            'location',
            'content',
        ];
    }

    public function getVirtualColumns(): array
    {
        return [
            'is_blacklist',
            'is_favorite',
            'amenities',
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
            'price',
            'price_on_request',
            'area',
            'created_at',
            'year_built',

            // --- From 'translations' table ---
            'location',
            'content',
        ];
    }

    /**
     * {@inheritdoc}
     * Defines the columns from the main 'property' table to be included in the global search.
     */
    public function getSearchableColumns(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     * Defines the columns from the 'property_translations' table to be included in the global search.
     */
    public function getSearchableTranslationColumns(): array
    {
        return ['location', 'content', 'meta_title'];
    }

    /**
     * {@inheritdoc}
     * This method is required by the interface to specify the translation table name.
     */
    public function getTranslationTable(): ?string
    {
        // Provide the exact name of your translation table.
        return 'property_translations';
    }

    /**
     * {@inheritdoc}
     * This method is required by the interface to specify the foreign key in the translation table.
     */
    public function getTranslationForeignKey(): ?string
    {
        // This is the column in 'property_translations' that links back to the 'property' table.
        return 'property_id';
    }


    /**
     * Get the formatted price for display.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    // protected function priceDisplay(): Attribute
    // {
    //     return Attribute::make(
    //         get: function ($value, $attributes) {
    //             if (isset($attributes['price_on_request']) || $attributes['price']>0) {
    //                 $formattedPrice = number_format($attributes['price'], 0, '.', ',');
    //                 $currency = $attributes['currency_code'] ?? __('SAR');
    //                 return $formattedPrice . ' ' . $currency;
    //             } else
    //             {
    //                 return __('app/properties.price_on_request');
    //             }
    //         }
    //     );
    // }
    protected function priceDisplay(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if ($attributes['price_on_request']) {
                    return __('app/properties.price_on_request');
                } else {
                    $formattedPrice = number_format($attributes['price'], 0, '.', ',');
                    $currency = $this->currency->name;
                    return $formattedPrice . ' ' . $currency;
                }
            }
        );
    }

    protected function publishedAtFormatted(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => (new Carbon($attributes['created_at']))->format('M d, Y g:i A T')
        );
    }

    public function name(): Attribute
    {

        return new Attribute(
            get: fn() => $this->defaultTranslation?->name,
        );
    }


    public function location(): Attribute
    {

        return new Attribute(
            get: fn() => $this->defaultTranslation?->location,
        );
    }

    public function slug(): Attribute
    {
        return new Attribute(
            get: fn() => $this->defaultTranslation?->slug,
        );
    }

    public function content(): Attribute
    {
        return new Attribute(
            get: fn() => $this->defaultTranslation?->content,
        );
    }

    public function metaTitle(): Attribute
    {
        return new Attribute(
            get: fn() => $this->defaultTranslation?->meta_title,
        );
    }

    public function metaDescription(): Attribute
    {
        return new Attribute(
            get: fn() => $this->defaultTranslation?->meta_description,
        );
    }

    public function metaKeywords(): Attribute
    {
        return new Attribute(
            get: fn() => $this->defaultTranslation?->meta_keywords
        );
    }

    /**
     * Get all of the translations for the Property
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations(): HasMany
    {
        return $this->hasMany(PropertyTranslation::class, foreignKey: 'property_id', localKey: 'id');
    }

    /**
     * Get all of the keywords for the Property
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function keywords(): HasMany
    {
        return $this->hasMany(PropertyKeyword::class, foreignKey: 'property_id', localKey: 'id');
    }



    /**
     * Get the defaultTranslation associated with the Property
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function defaultTranslation(): HasOne
    {
        return $this->hasOne(PropertyTranslation::class, 'property_id')
            // This is a modern way to create a "has one" that falls back.
            // It tries to get the current locale, if not found, it gets the first one.
            ->ofMany([], function ($query) {
                $query->where('locale', app()->getLocale());
            });
    }

    public function imageUrl(): Attribute
    {
        return  Attribute::get(function () {
            $image = $this->images->firstWhere('is_default', true);

            if (!$image && $this->images->isNotEmpty()) {
                $image = $this->images->random();
            }

            if (!$image) {
                return config('app.web_config.default_property_image');
            }
            return  $image->image_url;
        });
    }

    /**
     * Get the category that owns the Property
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function orientation(): BelongsTo
    {
        return $this->belongsTo(Orientation::class, 'orientation_id', 'id');
    }

    public function contractType(): BelongsTo
    {
        return $this->belongsTo(ContractType::class, 'contract_type_id', 'id');
    }

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class, 'floor_id', 'id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(PropertyStatus::class, 'status_id', 'id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'type_id', 'id');
    }

    public function condition(): BelongsTo
    {
        return $this->belongsTo(PropertyCondition::class, 'condition_id', 'id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    /**
     * Get all of the favorite for the Property
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function favorite(): HasMany
    {
        return $this->hasMany(Favorite::class, 'property_id', 'id');
    }

    /**
     * Get all of the amenity for the Property
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    // public function amenity(): HasMany
    public function propertyAmenity(): HasMany
    {
        return $this->hasMany(PropertyAmenity::class, 'amenity_id', 'id');
    }

    public function amenities()
    {
        return $this->belongsToMany(
            related: Amenity::class,
            table: 'property_amenities',
            foreignPivotKey: 'property_id',
            relatedPivotKey: 'amenity_id',
        );
    }

    /**
     * Get all of the images for the Property
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images(): HasMany
    {
        return $this->hasMany(PropertyGallery::class, 'property_id', 'id');
    }

    public function availableTimes(): HasMany
    {
        return $this->hasMany(PropertyAvailableTime::class, 'property_id', 'id');
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(PropertyFAQ::class, 'property_id', 'id');
    }


    /**
     * Get all of the propertyAttribute for the Property
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function propertyAttribute(): HasMany
    {
        return $this->hasMany(PropertyAttributeValue::class, 'property_id', 'id')->where('locale', app()->getLocale());
    }

    public function scopeFeatured(Builder $query)
    {
        $query->where('is_featured', true);
    }

    public function scopeNews(Builder $query)
    {
        $query->where('is_new', true);
    }

    public function scopeActive(Builder $query)
    {
        $query->where('status_id', 'active');
    }

    public function scopeSearch($query, $searchTerm)
    {

        $keywords = explode(' ', $searchTerm);

        foreach ($keywords as $keyword) {
            $query->where(function ($query) use ($keyword) {
                $query->withoutGlobalScope('has_translate')->whereHas('translations', function ($bquery) use ($keyword) {
                    $bquery->where(function ($query) use ($keyword) {
                        $query->where('pc_Property_translations.name', 'like', "%$keyword%");
                    });
                })->orWhereHas('keywords', function ($query) use ($keyword) {
                    $query->withoutGlobalScope('has_translate')->where('pc_Property_keywords.keyword', 'like', "%$keyword%");
                })->orWhereHas('category', function ($query) use ($keyword) {
                    $query->withoutGlobalScope('has_translate')->where(function ($cquery) use ($keyword) {
                        $cquery->whereHas('translations', function ($bquery) use ($keyword) {
                            $bquery->where('ec_category_translations.name', 'like', "%$keyword%");
                        })->orWhereHas('keywords', function ($bquery) use ($keyword) {
                            $bquery->where('ec_category_keywords.keyword', 'like', "%$keyword%");
                        });
                    });
                })->orWhereHas('brand', function ($query) use ($keyword) {
                    $query->withoutGlobalScope('has_translate')->where(function ($cquery) use ($keyword) {
                        $cquery->whereHas('translations', function ($bquery) use ($keyword) {
                            $bquery->where('ec_brand_translations.name', 'like', "%$keyword%");
                        })->orWhereHas('keywords', function ($bquery) use ($keyword) {
                            $bquery->where('ec_brand_keywords.keyword', 'like', "%$keyword%");
                        });
                    });
                });
            });
        }
        return $query;
    }

    public function updateCachedAmenities()
    {
        $amenityIds = $this->amenities()->pluck('amenities.id')->toArray();
        sort($amenityIds);
        $this->cached_amenities_ids = implode(',', $amenityIds);
        $this->save();
    }

    protected static function boot()
    {
        parent::boot();
    }
}
