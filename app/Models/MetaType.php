<?php

namespace App\Models;

use App\Traits\Cachable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property string $id
 * @property string $user_id
 * @property string $client_id
 * @property string $name
 * @property integer $type
 * @property string $validation
 * @property boolean $is_required
 * @property boolean $is_custom
 * @property integer $status
 * @property integer $weight
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 */
class MetaType extends BasicEntityModel
{

    use Cachable, SoftDeletes;

    public const STATUS_ACTIVE = 1;

    public const TYPE_USER_DATA  = 0;
    public const TYPE_COMPANY_DATA  = 1;

    public const IS_STANDARD     = false;
    public const IS_APP_SPECIFIC = true;

    public const USER_ID        = 'user_id';
    public const CLIENT_ID      = 'client_id';
    public const VALIDATION     = 'validation';
    public const IS_REQUIRED    = 'is_required';
    public const IS_CUSTOM      = 'is_custom';
    public const WEIGHT = 'weight';
    public const NAME   = 'name';
    public const TYPE   = 'type';
    public const STATUS = 'status';


    /**
     * @var array
     */
    protected $fillable = [self::CLIENT_ID, self::NAME, self::TYPE];

    protected $hidden = [self::WEIGHT, self::VALIDATION, self::IS_REQUIRED, self::IS_CUSTOM, self::STATUS,
        self::CREATED_AT, self::UPDATED_AT, self::DELETED_AT, self::USER_ID, self::PIVOT];


    /**
     * @return array
     */
    public static function getAllAvailableStatuses(): array
    {
        return [
            self::STATUS_ACTIVE,
        ];
    }


    /**
     * @return array
     */
    public static function getAllAvailableTypes(): array
    {
        return [
            self::TYPE_USER_DATA,
            self::TYPE_COMPANY_DATA,
        ];
    }


    protected static function boot()
    {
        parent::boot();
        static::creating(
            function (MetaType $model) {
                $model->{$model->getKeyName()} = (string) Str::orderedUuid();
                if (\Auth::hasUser()) {
                    $model->user_id = \Auth::id();
                }
            }
        );
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }


}
