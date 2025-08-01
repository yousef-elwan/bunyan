<?php

namespace App\Models\Floor;

use App\Models\GeneralModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * Class FloorTranslation
 *
 * @property int $id Primary
 * @property mixed $name
 * @property mixed $locale
 * @property int $floor_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @package App\Models
 */
class FloorTranslation extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "floors_translations";

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

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class, foreignKey: 'floor_id', ownerKey: 'id');
    }

    protected static function boot()
    {
        parent::boot();

    }
}
