<?php

namespace App\Models\Slider;

use App\Models\GeneralModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class SliderTranslation
 *
 * @property int $id Primary
 * @property string $title
 * @property string $sub_title
 * @property string $button_text
 * @property string $content
 * @property mixed $url
 * @property mixed $locale
 * @property int $slider_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @package App\Models
 */
class SliderTranslation extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "sliders_translations";

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
            'id' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the slider associated with the SliderTranslation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function slider(): BelongsTo
    {
        return $this->belongsTo(Slider::class, foreignKey: 'slider_id', ownerKey: 'id');
    }


    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            if (!isset($model->sub_title)) {
                $model->sub_title = "";
            }
            if (!isset($model->button_text)) {
                $model->button_text = "";
            }
            if (!isset($model->content)) {
                $model->content = "";
            }
            if (!isset($model->title)) {
                $model->title = "";
            }
        });
    }
}
