<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BasicEntityModel
 * @package App
 */
class BasicEntityModel extends Model
{
    use Uuids;

    public const ID = 'id';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const DELETED_AT = 'deleted_at';
    public const USER_ID    = 'user_id';
    public const PIVOT      = 'pivot';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $guarded = [self::CREATED_AT, self::UPDATED_AT, self::DELETED_AT, self::USER_ID];

    protected $hidden = [self::CREATED_AT, self::UPDATED_AT, self::DELETED_AT, self::USER_ID, self::PIVOT];

}
