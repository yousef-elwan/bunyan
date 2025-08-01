<?php

namespace App\Models\Property;

use App\Models\GeneralModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;


/**
 * Class PropertyGallery
 *
 * @property int $id Primary
 * @property int $property_id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @package App\Models
 */
class PropertyGallery extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "property_gallery";

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = ['id'];

    protected $appends = ['image_url'];


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
            'publish_at' => 'datetime',
        ];
    }

    /**
     * Get the property associated with the PropertyImage
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, foreignKey: 'property_id', ownerKey: 'id');
    }


    public function imageUrl(): Attribute
    {
        $image = $this->name;
        $property_id = $this->property_id;
        
        $value = !is_null($image) ?  asset('/' . PROPERTY_IMAGE_NAME . "/$property_id/images/$image") :
            config('app.web_config.default_property_image');

        return new Attribute(
            get: fn() => $value,
        );
    }
}
