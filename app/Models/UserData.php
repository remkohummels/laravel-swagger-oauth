<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

/**
 * Class UserData
 * @package App\Models
 */
class UserData extends Model
{
    public const ID = '_id';
    public const USER_ID = '___user_id___';
    public const NAME    = '___name___';
    public const CLIENT_REFERENCE = '___client_reference___';

    public const GROUP_WP_IMPORT = '___wp-import___';

    public const REQUIRED_FIELDS = [
        self::ID,
        self::USER_ID,
        self::CLIENT_REFERENCE,
        self::UPDATED_AT,
        self::CREATED_AT
    ];

    protected $collection = 'user_data';

    protected $connection = 'mongodb';

    protected $fillable = [self::USER_ID, self::CLIENT_REFERENCE, self::NAME];

    protected $guarded = [self::UPDATED_AT, self::CREATED_AT];

    protected $hidden = [self::UPDATED_AT, self::CREATED_AT];


    /**
     * @param array $attributes
     * @return array
     */
    protected function fillableFromArray(array $attributes)
    {
        return $attributes;
    }


    /**
     * @param string $key
     * @return bool
     */
    public function isFillable($key)
    {
        if ($this->isGuarded($key)) {
            return false;
        }

        return true;
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', self::USER_ID);
    }


}
