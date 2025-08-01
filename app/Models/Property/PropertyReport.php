<?php

namespace App\Models\Property;

use App\Models\GeneralModel;
use App\Models\ReportStatus\ReportStatus;
use App\Models\ReportType\ReportType;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;



/**
 * Class PropertyReport
 *
 * @property int $id Primary
 * @property int $user_id
 * @property int $property_id
 * @property int $type_id
 * @property string $message
 * @property mixed $name
 * @property mixed $email
 * @property mixed $mobile
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property mixed $report_status_id
 *
 * @package App\Models
 */
class PropertyReport extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "reports";

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

    public function property()
    {

        return $this->belongsTo(Property::class, 'property_id');
    }

    public function type()
    {
        return $this->belongsTo(ReportType::class, 'type_id', 'id');
    }

    public function reporter()
    {

        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function status()
    {
        return $this->belongsTo(ReportStatus::class, foreignKey: 'report_status_id', ownerKey: 'id');
    }
}
