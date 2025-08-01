<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Class BusinessSetting
 *
 * @property mixed $type
 * @property string $value
 * @property mixed $number
 * @property string $comment
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class BusinessSetting extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "business_settings";

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = ['id'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
