<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Appointment
 *
 * @property int $id Primary
 * @property int $user_id
 * @property int $client_id
 * @property int $property_id
 * @property mixed $title
 * @property string $notes
 * @property \Carbon\Carbon $date
 * @property mixed $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class Appointment extends GeneralModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'client_id',
        'property_id',
        'title',
        'notes',
        'date',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'datetime',
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
            'client_id',
            'property_id',
            'title',
            'notes',
            'date',
            'status',
        ];
    }

    /**
     * {@inheritdoc}
     * Defines the whitelist of columns that can be used for sorting.
     */
    public function getSortableColumns(): array
    {
        return [
            'title',
            'notes',
            'date',
            'status',
        ];
    }

    /**
     * {@inheritdoc}
     * Defines the columns from the main table to be included in the global search.
     */
    public function getSearchableColumns(): array
    {
        return [
            'title',
            'notes',
        ];
    }



    /**
     * Get the user (agent/admin) that owns the appointment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the client associated with the appointment.
     */
    public function client(): BelongsTo
    {
        // Assuming your clients are also in the 'users' table.
        // If you have a separate 'clients' table, change User::class to Client::class.
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the property associated with the appointment.
     */
    public function property(): BelongsTo
    {
        // Make sure you have a Property model
        return $this->belongsTo(\App\Models\Property\Property::class, 'property_id');
    }
}
