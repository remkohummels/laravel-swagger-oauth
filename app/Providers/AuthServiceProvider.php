<?php

namespace App\Providers;

use App\Passport\Bridge\UserRepository as WpUserRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Bridge\UserRepository;
use Laravel\Passport\Passport;
use Laravel\Passport\RouteRegistrar;

class AuthServiceProvider extends ServiceProvider
{
    // TODO move constants from here
    const ROLE_ADMINISTRATOR = 'administrator';
    const ROLE_CLIENT_APP = 'client_app';
    const ROLE_COMPANY = 'company';
    const ROLE_STAFF = 'staff';
    const ROLE_USER = 'user';

    const MANAGE_USERS = 'manage-users';
    const READ_USERS = 'read-users';
    const ASSIGN_STAFF = 'assign-staff';
    const MANAGE_COMPANIES = 'manage-companies';
    const READ_COMPANIES = 'read-companies';
    const MANAGE_TEAMS = 'manage-teams';
    const READ_TEAMS = 'read-teams';
    const MANAGE_META_TYPES = 'manage-meta-types';
    const READ_META_TYPES = 'read-meta-types';

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        app()->bind(UserRepository::class, WpUserRepository::class );

        Passport::routes(function (RouteRegistrar $router) {
            $router->forAccessTokens();
            $router->forAuthorization();
            $router->forTransientTokens();
            $router->forClients();
        });

        Passport::tokensExpireIn(Carbon::now()->addMinutes(60));

        Passport::refreshTokensExpireIn(Carbon::now()->addDays(10));

        Passport::tokensCan(self::getAllScopes());

        Passport::setDefaultScope(array_keys(self::getReadScopes()));
    }


    static public function getManageScopes(): array
    {
        return [
            self::MANAGE_USERS => 'Manage users',
            self::ASSIGN_STAFF => 'Assign staff to a company',
            self::MANAGE_COMPANIES => 'Manage companies',
            self::MANAGE_TEAMS => 'Manage teams',
            self::MANAGE_META_TYPES => 'Manage meta types',
        ];
    }

    static public function getReadScopes(): array
    {
        return [
            self::READ_USERS => 'Read users',
            self::READ_COMPANIES => 'Read companies',
            self::READ_TEAMS => 'Read teams',
            self::READ_META_TYPES => 'Read meta types',
        ];
    }

    static public function getAllScopes(): array
    {
        return array_merge(self::getReadScopes(), self::getManageScopes());
    }
}
