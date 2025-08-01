<?php

namespace App\Models\Slider;

use App\Enums\SliderTypeEnum;
use App\Models\Category\Category;
use App\Models\GeneralModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;


/**
 * Class Slider
 *
 * @property int $id Primary
 * @property mixed $status
 * @property string $image
 * @property mixed $type
 * @property mixed $published
 * @property mixed $resource_type
 * @property int $resource_id
 * @property mixed $background_color
 * @property \Carbon\Carbon $from_time
 * @property \Carbon\Carbon $to_time
 * @property int $sort_number
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @package App\Models
 */
class Slider extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "sliders";

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = ['id'];

    protected $appends = ['image_url', 'title', 'sub_title', 'button_text', 'url', 'content'];

    // Preloading current locale translation at all times
    protected $with = [
        'defaultTranslation'
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
            'status',
            'image',
            'type',
            'published',
            'resource_type',
            'resource_id',
            'background_color',
            'from_time',
            'to_time',
            'sort_number',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',

            // --- From 'translations' table ---
            'title',
            'sub_title',
            'button_text',
            'content',
            'url',
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
            'status',
            'image',
            'type',
            'published',
            'resource_type',
            'resource_id',
            'background_color',
            'from_time',
            'to_time',
            'sort_number',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',

            // --- From 'translations' table ---
            'title',
            'sub_title',
            'button_text',
            'content',
            'url',
        ];
    }

    /**
     * {@inheritdoc}
     * Defines the columns from the main  table to be included in the global search.
     */
    public function getSearchableColumns(): array
    {
        return [
            'id',
            'status',
            'image',
            'type',
            'published',
            'resource_type',
            'resource_id',
            'background_color',
            'from_time',
            'to_time',
            'sort_number',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
        ];
    }

    /**
     * {@inheritdoc}
     * Defines the columns from the 'translations' table to be included in the global search.
     */
    public function getSearchableTranslationColumns(): array
    {
        return [
            'title',
            'sub_title',
            'button_text',
            'content',
            'url',
        ];
    }

    /**
     * {@inheritdoc}
     * This method is required by the interface to specify the translation table name.
     */
    public function getTranslationTable(): ?string
    {
        // Provide the exact name of your translation table.
        return 'sliders_translations';
    }

    /**
     * {@inheritdoc}
     * This method is required by the interface to specify the foreign key in the translation table.
     */
    public function getTranslationForeignKey(): ?string
    {
        // This is the column in 'translations' that links back to the 'property' table.
        return 'slider_id';
    }

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
            'publish_at' => 'datetime',
        ];
    }


    public function content(): Attribute
    {

        return new Attribute(
            get: fn() => $this->defaultTranslation?->content,
        );
    }

    public function title(): Attribute
    {

        return new Attribute(
            get: fn() => $this->defaultTranslation?->title,
        );
    }

    public function subTitle(): Attribute
    {

        return new Attribute(
            get: fn() => $this->defaultTranslation?->sub_title,
        );
    }

    public function buttonText(): Attribute
    {

        return new Attribute(
            get: fn() => $this->defaultTranslation?->button_text,
        );
    }

    public function url(): Attribute
    {

        return new Attribute(
            get: fn() => $this->defaultTranslation?->url,
        );
    }


    public function imageUrl(): Attribute
    {
        $image = $this->image;
        return new Attribute(
            get: fn() =>  isset($image) ?  Storage::disk('public')->url(SLIDER_IMAGE_NAME . "/" . $image) :
                config('app.web_config.default_slider_image'),
        );
    }

    /**
     * Get all of the translations for the Slider
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations(): HasMany
    {
        return $this->hasMany(SliderTranslation::class, foreignKey: 'slider_id', localKey: 'id');
    }

    /**
     * Get the defaultTranslation associated with the Blog
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function defaultTranslation(): HasOne
    {
        return $this->hasOne(SliderTranslation::class, 'slider_id')
            // This is a modern way to create a "has one" that falls back.
            // It tries to get the current locale, if not found, it gets the first one.
            ->ofMany([], function ($query) {
                $query->where('locale', app()->getLocale());
            });
    }

    public function scopePublished(Builder $query)
    {
        $query->where('published', true);
    }

    public function scopePopup(Builder $query)
    {
        $query->where('type', SliderTypeEnum::popup);
    }

    public function scopeHeader(Builder $query)
    {
        $query->where('type', SliderTypeEnum::header);
    }

    public function scopeFooter(Builder $query)
    {
        $query->where('type', SliderTypeEnum::footer);
    }

    public function scopeActive(Builder $query)
    {
        $query->where('status', true);
    }

    /**
     * Get the category that owns the Slider
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return   $this->belongsTo(Category::class, 'resource_id', 'id');
        // ->where(function ($cond) {
        //     info($this);
        //     if ($this->resource_type != 'category') {
        //         $cond->where('id', null);
        //     }
        // });
    }
    public function scopeActiveInPeriod(Builder $query)
    {
        $now = now();
        $query->where('status', true)
            ->where(function ($wq) use ($now) {
                $wq->whereRaw(
                    "(from_time IS NULL AND to_time IS NULL)"
                )->orWhereRaw(
                    "(
                    ( from_time IS NOT NULL AND to_time IS NOT NULL) AND ('$now' <= to_time AND '$now' >= from_time )
                )"
                )->orWhereRaw(
                    "(
                    ( from_time IS NOT NULL AND to_time IS NULL) AND  ( '$now' >= from_time)
                )"
                )->orWhereRaw(
                    "(
                    ( from_time IS  NULL AND to_time IS NOT NULL) AND  ('$now' <= to_time)
                )"
                );
            });
    }

    protected static function boot()
    {
        parent::boot();
        // static::addGlobalScope('has_translate', function (Builder $builder) {
        //     $builder->whereHas('defaultTranslation', function ($q) {
        //         $q->where('title', '!=', '');
        //     });
        // });
    }
}
