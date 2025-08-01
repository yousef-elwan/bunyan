<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Lang
 *
 * @property int $id Primary
 * @property mixed $locale
 * @property mixed $name
 * @property mixed $direction
 * @property mixed $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @package App\Models
 */
class Lang extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "langs";

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [];


    /**
     * {@inheritdoc}
     * Defines the whitelist of columns that can be filtered by the front-end.
     * This includes columns from the main table and the translation table.
     */
    public function getFilterableColumns(): array
    {
        return [
            'id',
            'locale',
            'name',
            'direction',
            'is_active',
            'created_at',
            'updated_at',
            'created_by',
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
            'locale',
            'name',
            'direction',
            'is_active',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
        ];
    }
    
    
    /**
     * {@inheritdoc}
     * Defines the columns from the main table to be included in the global search.
     */
    public function getSearchableColumns(): array
    {
        return [
            'name',
            'locale',
        ];
    }


    public function scopeActive(Builder $query)
    {
        $query->where('is_active', true);
    }
}
