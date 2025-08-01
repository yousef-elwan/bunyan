<?php

namespace App\Models\ReportType;

use App\Models\GeneralModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class ReportType
 *
 * @property int $id Primary
 * @property \Carbon\Carbon $created_at
 * @property int $created_by
 * @property \Carbon\Carbon $updated_at
 * @property int $updated_by
 *
 * @package App\Models
 */
class ReportType extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "report_types";

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
     * Defines the columns from the main table to be included in the global search.
     */
    public function getSearchableColumns(): array
    {
        return [];
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
        return 'report_types_translations';
    }

    /**
     * {@inheritdoc}
     * This method is required by the interface to specify the foreign key in the translation table.
     */
    public function getTranslationForeignKey(): ?string
    {
        // This is the column in 'translations' that links back to the 'type' table.
        return 'report_type_id';
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

    public function translations(): HasMany
    {
        return $this->hasMany(ReportTypeTranslation::class, foreignKey: 'report_type_id', localKey: 'id');
    }

    /**
     * Get the defaultTranslation associated with the ReportType
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function defaultTranslation(): HasOne
    {
        return $this->hasOne(ReportTypeTranslation::class, 'report_type_id')
            // This is a modern way to create a "has one" that falls back.
            // It tries to get the current locale, if not found, it gets the first one.
            ->ofMany([], function ($query) {
                $query->where('locale', app()->getLocale());
            });
    }

    protected static function boot()
    {
        parent::boot();
    }
}
