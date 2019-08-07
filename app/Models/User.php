<?php

namespace App\Models;

use App\Traits\PasswordAuthentication;
use App\Traits\TwoFATrait;
use App\Traits\Uuids;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Jenssegers\Mongodb\Eloquent\Builder;
use Jenssegers\Mongodb\Eloquent\HybridRelations;
use Laratrust\Traits\LaratrustUserTrait;
use Laravel\Passport\HasApiTokens;
use Laravel\Scout\Searchable;
use Lcobucci\JWT\Parser;

/**
 * @property string $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property Collection $teams
 * @property UserData[] $applicationData
 * @property UserData[] $defaultData
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use LaratrustUserTrait, HybridRelations;
    use Uuids, Notifiable, HasApiTokens, PasswordAuthentication, TwoFATrait, Searchable, CanResetPassword;

    public const STATUS_APPROVED       = 'approved';
    public const STATUS_TO_BE_APPROVED = 'to be approved';

    public const ID         = 'id';
    public const NAME       = 'name';
    public const FIRST_NAME = 'first_name';
    public const LAST_NAME  = 'last_name';
    public const EMAIL      = 'email';
    public const USER_LITMOS_ID   = 'user_litmos_id';
    public const OLD_USER_ID      = 'old_user_id';
    public const WP_USER_ID       = 'wp_user_id';
    public const PASSWORD   = 'password';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const DELETED_AT = 'deleted_at';
    public const PIVOT      = 'pivot';
    public const APPLICATION_DATA  = 'applicationData';
    public const DEFAULT_DATA      = 'defaultData';
    public const REMEMBER_TOKEN    = 'remember_token';
    public const GOOGLE_2FA_SECRET = 'google_2fa_secret';
    public const IS_2FA_ENABLED    = 'is_2fa_enabled';
    public const EMAIL_VERIFIED_AT = 'email_verified_at';

    public const BASIC_GROUP = '___basic___';

    protected $keyType = 'string';

    public $incrementing = false;

    public $asYouType = false;

    protected $fillable = [
        self::NAME, self::FIRST_NAME, self::LAST_NAME, self::EMAIL, self::USER_LITMOS_ID, self::OLD_USER_ID,
        self::WP_USER_ID, self::PASSWORD, self::IS_2FA_ENABLED
    ];

    protected $guarded = [self::APPLICATION_DATA, self::DEFAULT_DATA, self::PASSWORD, self::REMEMBER_TOKEN, self::GOOGLE_2FA_SECRET,
        self::CREATED_AT, self::UPDATED_AT];

    protected $hidden = [
        self::PASSWORD, self::REMEMBER_TOKEN, self::IS_2FA_ENABLED, self::GOOGLE_2FA_SECRET, self::EMAIL_VERIFIED_AT, self::PIVOT,
        self::APPLICATION_DATA, self::DEFAULT_DATA, self::UPDATED_AT
    ];


    /**
     * @return array
     */
    public static function getAllAvailableStatuses(): array
    {
        return [
            self::STATUS_APPROVED,
            self::STATUS_TO_BE_APPROVED,
        ];
    }


    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        return $array;
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function teams()
    {
        return $this->belongsToMany('App\Models\Team', 'team_user', 'user_id', 'team_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function applicationData()
    {
        if (request()->bearerToken() != null) {
            $clientId = (new Parser())->parse(request()->bearerToken())->getClaim('aud');
        } else {
            $clientId = 2;
        }

        return $this->hasMany('App\Models\UserData', UserData::USER_ID)->where(UserData::CLIENT_REFERENCE, $clientId);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function defaultData()
    {
        return $this->hasMany('App\Models\UserData', UserData::USER_ID)->where(UserData::CLIENT_REFERENCE, 'exists', false);
    }

}
