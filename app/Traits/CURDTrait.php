<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

trait CURDTrait
{

    public static function bootCURDTrait()
    {
        static::creating(function (Model $model) {
            if (hasColumn($model, 'created_at')) {
                $model->created_at = now();
            }
            if (hasColumn($model, 'created_by')) {
                $model->created_by = auth()->user()?->{(new User())->getKeyName()} ?? null;
            }
        });

        static::updating(function (Model $model) {
            if (hasColumn($model, 'updated_at')) {
                $model->updated_at = now();
            }
            if (hasColumn($model, 'updated_by')) {
                $model->updated_by = auth()->user()?->{(new User())->getKeyName()} ?? null;
            }
        });

        static::deleting(function (Model $model) {
            if (hasColumn($model, 'deleted_at')) {
                $model->updated_at = now();
            }
            if (hasColumn($model, 'deleted_by')) {
                $model->updated_by = auth()->user()?->{(new User())->getKeyName()} ?? null;
            }
        });
    }
}
