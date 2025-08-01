<?php

namespace App\Models\PageMeta;

use App\Models\GeneralModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class PageMetaTranslation
 *
 * @property int $id Primary
 * @property mixed $title
 * @property string $description
 * @property mixed $keywords
 * @property mixed $locale
 * @property int $pages_meta_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @package App\Models
 */
class PageMetaTranslation extends GeneralModel
{
    /**
     * Table Name In Database.
     */
    protected $table = "pages_meta_translations";

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

    public function PageMeta(): BelongsTo
    {
        return $this->belongsTo(PageMeta::class, foreignKey: 'pages_meta_id', ownerKey: 'id');
    }

    protected static function boot()
    {
        parent::boot();
    }
}
