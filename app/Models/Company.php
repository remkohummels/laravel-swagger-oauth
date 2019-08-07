<?php

namespace App\Models;

use App\Traits\Cachable;
use App\Traits\UuidsCreatedBy;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Mongodb\Eloquent\HybridRelations;
use Lcobucci\JWT\Parser;

/**
 * @property string $id
 * @property string $user_id
 * @property string $name
 * @property string $address
 * @property string $phone
 * @property string $created_at
 * @property string $updated_at
 * @property string $zip
 * @property string $fax
 * @property string $website
 * @property string $language
 * @property string $location
 * @property string $description
 * @property string $short_description
 * @property boolean $opened_24_hours
 * @property string $payment_method
 * @property string $facebook_url
 * @property string $key_person_name
 * @property string $key_person_title
 * @property string $key_person_phone
 * @property string $key_person_email
 * @property string $eligibility_requirement
 * @property string $deleted_at
 * @property User $user
 * @property Team[] $teams
 * @property CompanyData[] $companyData
 * @property CompanyData[] $applicationData
 * @property CompanyData[] $defaultData
 */
class Company extends BasicEntityModel
{
    use UuidsCreatedBy, HybridRelations, SoftDeletes;

    public const NAME     = 'name';
    public const ADDRESS  = 'address';
    public const ZIP      = 'zip';
    public const PHONE    = 'phone';
    public const FAX      = 'fax';
    public const WEBSITE  = 'website';
    public const LANGUAGE = 'language';
    public const LOCATION = 'location';
    public const APPLICATION_DATA  = 'applicationData';
    public const DEFAULT_DATA      = 'defaultData';
    public const DESCRIPTION       = 'description';
    public const SHORT_DESCRIPTION = 'short_description';
    public const OPENED_24_HOURS   = 'opened_24_hours';
    public const PAYMENT_METHOD    = 'payment_method';
    public const FACEBOOK_URL      = 'facebook_url';
    public const KEY_PERSON_NAME   = 'key_person_name';
    public const KEY_PERSON_TITLE  = 'key_person_title';
    public const KEY_PERSON_PHONE  = 'key_person_phone';
    public const KEY_PERSON_EMAIL  = 'key_person_email';
    public const ELIGIBILITY_REQUIREMENT = 'eligibility_requirement';

    public const USER_ID = 'user_id';

    public const BASIC_GROUP = '___basic___';

    protected $fillable = [self::NAME, self::ADDRESS, self::ZIP, self::PHONE, self::FAX, self::WEBSITE, self::DESCRIPTION,
        self::SHORT_DESCRIPTION, self::OPENED_24_HOURS, self::LANGUAGE, self::PAYMENT_METHOD, self::LOCATION, self::FACEBOOK_URL,
        self::ELIGIBILITY_REQUIREMENT, self::KEY_PERSON_NAME, self::KEY_PERSON_TITLE, self::KEY_PERSON_PHONE, self::KEY_PERSON_EMAIL,
        self::USER_ID];

    protected $guarded = [self::APPLICATION_DATA, self::DEFAULT_DATA, self::CREATED_AT, self::UPDATED_AT, self::DELETED_AT, self::USER_ID];
    protected $hidden  = [self::APPLICATION_DATA, self::DEFAULT_DATA, self::CREATED_AT, self::UPDATED_AT, self::DELETED_AT, self::USER_ID, self::PIVOT];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teams(): HasMany
    {
        $clientId = (new Parser())->parse(request()->bearerToken())->getClaim('aud');
        return $this->hasMany('App\Models\Team')->where(Team::CLIENT_ID, $clientId);
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function companyData()
    {
        return $this->hasMany('App\Models\CompanyData');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function applicationData()
    {
        $clientId = (new Parser())->parse(request()->bearerToken())->getClaim('aud');
        return $this->hasMany('App\Models\CompanyData')->where(CompanyData::CLIENT_REFERENCE, $clientId);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function defaultData()
    {
        return $this->hasMany('App\Models\CompanyData')->where(CompanyData::CLIENT_REFERENCE, 'exists', false);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

}
