<?php

namespace App\Models;


/**
 * Class Contact
 *
 * @property int $id Primary
 * @property mixed $name
 * @property mixed $email
 * @property mixed $mobile
 * @property mixed $subject
 * @property string $message
 * @property \Carbon\Carbon $created_at
 * @property int $created_by
 * @property \Carbon\Carbon $updated_at
 * @property int $updated_by
 *
 * @package App\Models
 */
class Contact extends GeneralModel
{
    protected $table = 'contacts';

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'subject',
        'message',
    ];
    protected $guarded = [
        'id',
    ];


    /**
     * {@inheritdoc}
     * Defines the whitelist of columns that can be filtered by the front-end.
     * This includes columns from the main table and the translation table.
     */
    public function getFilterableColumns(): array
    {
        return [
            'id',
            'name',
            'email',
            'mobile',
            'subject',
            'message',
            'created_at',
        ];
    }

    /**
     * {@inheritdoc}
     * Defines the whitelist of columns that can be used for sorting.
     */
    public function getSortableColumns(): array
    {
        return [
            'name',
            'email',
            'mobile',
            'subject',
            'message',
            'created_at',
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
            'email',
            'mobile',
            'subject',
            'message',
        ];
    }
}
