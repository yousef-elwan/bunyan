<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


/**
 * Class NewsletterSubscriber
 *
 * @property int $id Primary
 * @property mixed $email
 * @property mixed $token
 * @property \Carbon\Carbon $verified_at
 * @property mixed $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class NewsletterSubscriber extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "subscribers";

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
            'email',
            'token',
            'verified_at',
            'is_active',
            'created_at',
            'updated_at',
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
            'email',
            'token',
            'verified_at',
            'is_active',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * {@inheritdoc}
     * Defines the columns from the main table to be included in the global search.
     */
    public function getSearchableColumns(): array
    {
        return [
            'email',
        ];
    }


    public function scopeActive(Builder $query)
    {
        $query->where('is_active', true);
    }
}
