<?php

namespace App\Models;

use App\Models\Property\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * Class UserPrivateComment
 *
 * @property int $id Primary
 * @property int $user_id
 * @property int $property_id
 * @property string $comment
 *
 * @package App\Models
 */
class UserPrivateComment extends GeneralModel
{

    protected $table = "user_comments";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'property_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'property_id' => 'integer',
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
            'property_id',
            'comment',
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
            'property_id',
            'comment',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
