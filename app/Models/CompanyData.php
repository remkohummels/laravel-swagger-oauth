<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

/**
 * Class CompanyData
 * @package App\Models
 */
class CompanyData extends Model
{
    public const COMPANY_ID = 'company_id';
    public const NAME    = '___name___';
    public const CLIENT_REFERENCE = 'client_reference';

    public const REQUIRED_FIELDS = [
        '_id',
        self::COMPANY_ID,
        self::CLIENT_REFERENCE,
        self::UPDATED_AT,
        self::CREATED_AT
    ];

    protected $collection = 'company_data';

    protected $connection = 'mongodb';

    protected $fillable = [self::COMPANY_ID, self::CLIENT_REFERENCE, self::NAME];

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
    public function company()
    {
        return $this->belongsTo('App\Models\Company', self::COMPANY_ID);
    }


}
