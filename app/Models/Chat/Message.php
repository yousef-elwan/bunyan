<?php

namespace App\Models\Chat;

use App\Models\GeneralModel;
use App\Models\Property\Property;
use App\Models\Property\PropertyAvailableTime;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * Class Message
 *
 * @property int $id Primary
 * @property int $conversation_id
 * @property int $topic_id
 * @property int $user_id
 * @property int $property_id
 * @property string $message
 * @property mixed $name
 * @property mixed $email
 * @property mixed $mobile
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @package App\Models
 */
class Message extends GeneralModel
{

    protected $table = "messages";
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [
        'id'
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'sender_user_id' => 'integer',
        'property_id' => 'integer',
        'topic_id' => 'integer',
        'created_at' => 'datetime',
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
            'conversation_id',
            'topic_id',
            'user_id',
            'property_id',
            'message',
            'name',
            'email',
            'mobile',
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
            'topic_id',
            'user_id',
            'property_id',
            'message',
            'name',
            'email',
            'mobile',
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
            'message',
        ];
    }


    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class, 'topic_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id');
    }
}
