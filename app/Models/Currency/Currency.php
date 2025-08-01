<?php

namespace App\Models\Currency;

use App\Models\GeneralModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Currency
 *
 * @property int $id Primary
 * @property mixed $name
 * @property mixed $is_active
 * @property mixed $symbol
 * @property mixed $code
 * @property float $exchange_rate
 * @property \Carbon\Carbon $created_at
 * @property int $created_by
 * @property \Carbon\Carbon $updated_at
 * @property int $updated_by
 *
 * @package App\Models
 */
class Currency extends GeneralModel
{

    protected $table = 'currencies';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $guarded = [
        'id',
    ];

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
        return $this->hasMany(CurrencyTranslation::class, foreignKey: 'currency_id', localKey: 'id');
    }

    /**
     * Get the defaultTranslation associated with the ContractType
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function defaultTranslation(): HasOne
    {
        return $this->hasOne(CurrencyTranslation::class, 'currency_id')
            // This is a modern way to create a "has one" that falls back.
            // It tries to get the current locale, if not found, it gets the first one.
            ->ofMany([], function ($query) {
                $query->where('locale', app()->getLocale());
            });
    }

    /**
     * {@inheritdoc}
     * Defines the whitelist of columns that can be filtered by the front-end.
     * This includes columns from the main table and the translation table.
     */
    public function getFilterableColumns(): array
    {
        return [
            'id',
            'is_active',
            'symbol',
            'code',
            'exchange_rate',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
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
            'name',
            'is_active',
            'symbol',
            'code',
            'exchange_rate',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
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

    public function scopeActive(Builder $query)
    {
        $query->where('is_active', true);
    }

    protected static function boot(): void
    {
        parent::boot();
    }
}
