<?php

namespace App\Models\ContractType;

use App\Models\GeneralModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ContractTypeTranslation
 *
 * @property int $id Primary
 * @property mixed $name
 * @property mixed $locale
 * @property int $contract_type_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @package App\Models
 */
class ContractTypeTranslation extends GeneralModel
{
    /**
     * Table Name In Database.
     */
    protected $table = "contract_types_translations";

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

    public function contractType(): BelongsTo
    {
        return $this->belongsTo(ContractType::class, foreignKey: 'contract_type_id', ownerKey: 'id');
    }

    protected static function boot()
    {
        parent::boot();
    }
}
