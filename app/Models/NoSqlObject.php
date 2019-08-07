<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

/**
 * Class NoSqlObject
 * @package App\Models
 */
class NoSqlObject extends Model
{
    use SoftDeletes;

    public const REFERENCE   = 'reference';
    public const RELATION_ID = 'relation_id';

    public const REQUIRED_FIELDS = [
        '_id',
        self::RELATION_ID,
        self::REFERENCE,
        self::UPDATED_AT,
        self::CREATED_AT
    ];

    protected $collection = 'objects';

    protected $connection = 'mongodb';

    protected $fillable = [self::REFERENCE];

    protected $guarded = [self::RELATION_ID, self::UPDATED_AT, self::CREATED_AT];

    protected $hidden = [self::RELATION_ID, self::UPDATED_AT, self::CREATED_AT];


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
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function buildingObject()
    {
        return $this->hasOne('App\Models\BuildingObject', BuildingObject::DATA_ID);
    }


}
