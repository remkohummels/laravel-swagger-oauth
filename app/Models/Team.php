<?php

namespace App\Models;

use App\Traits\Cachable;

/**
 * @property string $id
 * @property string $company_id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property Company $company
 * @property User[] $users
 */
class Team extends BasicEntityModel
{
    use Cachable;

    public const NAME       = 'name';
    public const COMPANY_ID = 'company_id';
    public const CLIENT_ID  = 'client_id';

    protected $fillable = [self::COMPANY_ID, self::NAME];

    protected $hidden = [self::CLIENT_ID, self::CREATED_AT, self::UPDATED_AT, self::DELETED_AT, self::USER_ID, self::PIVOT];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'team_user', 'team_id', 'user_id');
    }


}
