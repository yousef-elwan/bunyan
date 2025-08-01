<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Subscription
 *
 * @property int $id Primary
 * @property int $user_id
 * @property int $admin_id
 * @property mixed $package_name
 * @property mixed $duration_in_days
 * @property \Carbon\Carbon $start_date
 * @property \Carbon\Carbon $end_date
 * @property float $price
 * @property mixed $payment_method
 * @property string $notes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @package App\Models
 */
class Subscription extends GeneralModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admin_id',
        'package_name',
        'duration_in_days',
        'start_date',
        'end_date',
        'price',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
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
            'user_id',
            'admin_id',
            'package_name',
            'duration_in_days',
            'start_date',
            'end_date',
            'price',
            'payment_method',
            'notes',
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
            'user_id',
            'admin_id',
            'package_name',
            'duration_in_days',
            'start_date',
            'end_date',
            'price',
            'payment_method',
            'notes',
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
            'package_name',
            'duration_in_days',
            'start_date',
            'end_date',
            'price',
            'payment_method',
            'notes',
        ];
    }



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
