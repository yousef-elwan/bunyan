<?php

namespace App\Models;

use App\Models\GeneralModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ContactUs
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
class ContactUs extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "contacts";

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [
        'id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
