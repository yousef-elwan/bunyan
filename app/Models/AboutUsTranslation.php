<?php

namespace App\Models;

use App\Models\GeneralModel;
use Illuminate\Database\Eloquent\Model;


/**
 * Class AboutUsTranslation
 *
 * @property int $id Primary
 * @property mixed $locale
 * @property string $content
 * @property mixed $meta_title
 * @property string $meta_description
 * @property \Carbon\Carbon $created_at
 * @property int $created_by
 * @property \Carbon\Carbon $updated_at
 * @property int $updated_by
 *
 * @package App\Models
 */
class AboutUsTranslation extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "about_us_translations";

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


    protected static function boot()
    {
        parent::boot();
        static::creating(function (Model $model) {
            if (!isset($model->title)) {
                $model->title = "";
            }
            if (!isset($model->content)) {
                $model->content = "";
            }
            if (!isset($model->meta_title)) {
                $model->meta_title = "";
            }
            if (!isset($model->meta_description)) {
                $model->meta_description = "";
            }
        });
    }
}

