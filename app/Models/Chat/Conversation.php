<?php

namespace App\Models\Chat;

use App\Models\GeneralModel;
use App\Models\Property\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Conversation
 *
 * @property int $id Primary
 * @property int $last_message_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class Conversation extends GeneralModel
{


    protected $guarded = [];

    protected $with = ['topics', 'lastMessage'];



    /**
     * {@inheritdoc}
     * Defines the whitelist of columns that can be filtered by the front-end.
     * This includes columns from the main table and the translation table.
     */
    public function getFilterableColumns(): array
    {
        return [
            // Columns from the  table
            'last_message_id',
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
            'last_message_id',
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


    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
            ->withPivot('read_at')
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->latest();
    }

    // public function lastMessage(): BelongsTo
    // {
    //     // هذه العلاقة مفيدة جدًا لعرض آخر رسالة في قائمة المحادثات
    //     return $this->belongsTo(Message::class, 'last_message_id');
    // }
    // public function lastMessage()
    // {
    //     return $this->hasOne(Message::class)
    //         ->latestOfMany();
    // }


    public function lastMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'last_message_id', 'id');
    }


    public function topicsCount()
    {
        return $this->hasMany(Topic::class)->selectRaw('conversation_id, count(*) as count')
            ->groupBy('conversation_id');
    }

    // public function property(): BelongsTo
    // {
    //     return $this->belongsTo(Property::class);
    // }
    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class);
    }
}
