<?php

namespace App\Models\Chat;

use App\Models\GeneralModel;
use App\Models\Property\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Topic
 *
 * @property int $id Primary
 * @property int $conversation_id
 * @property int $property_id
 * @property mixed $title
 * @property mixed $is_closed
 * @property int $last_message_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @package App\Models
 */
class Topic extends GeneralModel
{
    protected $guarded = [];



    /**
     * {@inheritdoc}
     * Defines the whitelist of columns that can be filtered by the front-end.
     * This includes columns from the main table and the translation table.
     */
    public function getFilterableColumns(): array
    {
        return [
            'conversation_id',
            'property_id',
            'title',
            'is_closed',
            'last_message_id',
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
            'conversation_id',
            'property_id',
            'title',
            'is_closed',
            'last_message_id',
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
            'title'
        ];
    }



    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function lastMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }
}
