<?php

namespace App\Models\PropertyFAQ;

use App\Models\GeneralModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;



/**
 * Class PropertyFAQTranslation
 *
 * @property int $id Primary
 * @property mixed $title
 * @property string $content
 * @property mixed $locale
 * @property int $property_faq_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @package App\Models
 */
class PropertyFAQTranslation extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "property_faq_translations";

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

    public function propertyFaq(): BelongsTo
    {
        return $this->belongsTo(PropertyFAQ::class, foreignKey: 'property_faq_id', ownerKey: 'id');
    }

    protected static function boot()
    {
        parent::boot();
    }
}
