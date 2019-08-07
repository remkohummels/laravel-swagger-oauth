<?php

namespace App\Traits;

use Illuminate\Support\Str;

/**
 * Trait UuidsCreatedBy
 * @package App\Traits
 */
trait UuidsCreatedBy
{


    protected static function boot()
    {
        parent::boot();
        static::creating(
            function ($model) {
                $model->{$model->getKeyName()} = (string) Str::orderedUuid();

                if (\Auth::hasUser()) {
                    $model->user_id = \Auth::id();
                }
            }
        );
    }


}