<?php

use App\Providers\AuthServiceProvider;

Route::post('login', 'Admin\Oauth2Controller@passwordAPILogin');
Route::post('/login/refresh', 'Admin\Oauth2Controller@refresh');

Route::prefix('2fa')
    ->group(
        function () {
            Route::post('enable', 'TwoFAController@enable2FA');
            Route::post('disable', 'TwoFAController@disable2FA');
            Route::get('get-code', 'TwoFAController@get2FACode');
            Route::post('verify-secret', 'TwoFAController@verifySecret');
        }
    );

$indexShowUpdateOnly = ['except' => ['store', 'destroy']];
$indexShowOnly = ['except' => ['store', 'update', 'destroy']];
$updateOnly = ['except' => ['index', 'show', 'store', 'destroy']];
$storeDestroyOnly = ['except' => ['index', 'show', 'update']];

$companyUsers = implode('|', [AuthServiceProvider::ROLE_COMPANY, AuthServiceProvider::ROLE_ADMINISTRATOR]);

$setStandardGroup = function ($routeName, $controller, $readScope, $manageScope) use ($companyUsers) {
    $staffCompanyUsers = implode('|', [AuthServiceProvider::ROLE_STAFF, AuthServiceProvider::ROLE_COMPANY, AuthServiceProvider::ROLE_ADMINISTRATOR]);

    $indexShowOnly = ['except' => ['store', 'update', 'destroy']];
    $updateOnly = ['except' => ['index', 'show', 'store', 'destroy']];
    $storeDestroyOnly = ['except' => ['index', 'show', 'update']];

    Route::group(
        ['middleware' => ['auth:api', 'role:' . $companyUsers, 'scope:' . $manageScope]],
        function () use ($routeName, $controller, $storeDestroyOnly) {
            Route::apiResource($routeName, $controller, $storeDestroyOnly);
        }
    );

    Route::group(
        ['middleware' => ['auth:api', 'role:' . $staffCompanyUsers, 'scope:' . $manageScope]],
        function () use ($routeName, $controller, $updateOnly) {
            Route::apiResource($routeName, $controller, $updateOnly);
        }
    );

    Route::group(
        ['middleware' => ['auth:api', 'scope:' . $readScope . ',' . $manageScope]],
        function () use ($routeName, $controller, $indexShowOnly) {
            Route::apiResource($routeName, $controller, $indexShowOnly);
        }
    );
};

$setStandardGroup(
    'companies',
    'CompanyController',
    AuthServiceProvider::READ_COMPANIES,
    AuthServiceProvider::MANAGE_COMPANIES
);

$setStandardGroup(
    'meta-types',
    'MetaTypeController',
    AuthServiceProvider::READ_META_TYPES,
    AuthServiceProvider::MANAGE_META_TYPES
);

$setStandardGroup(
    'teams',
    'TeamController',
    AuthServiceProvider::READ_TEAMS,
    AuthServiceProvider::MANAGE_TEAMS
);


Route::group(
    ['middleware' => ['auth:api', 'role:' . AuthServiceProvider::ROLE_ADMINISTRATOR, 'scope:' . AuthServiceProvider::MANAGE_USERS]],
    function () use ($storeDestroyOnly) {
        Route::apiResource('users', 'UserController', $storeDestroyOnly);
    }
);

Route::group(
    ['middleware' => ['auth:api', 'scope:' . AuthServiceProvider::READ_USERS . ',' . AuthServiceProvider::MANAGE_USERS]],
    function () use ($indexShowUpdateOnly) {
        Route::apiResource('users', 'UserController', $indexShowUpdateOnly);
    }
);

Route::group(['middleware' => ['guest']],
    function () {
        Route::post('password/forgot', 'Auth\ForgotPasswordController@sendResetLinkEmail');
        Route::post('register', 'Auth\RegisterController@registerApi');
    }
);
Route::group(['middleware' => ['auth:api']],
    function () {
        Route::post('password/reset', 'Auth\ResetPasswordController@resetLoggedIn');
        Route::get('current-user', 'UserController@current');
    }
);

Route::group(
    ['middleware' => ['auth:api', 'role:' . AuthServiceProvider::ROLE_ADMINISTRATOR . '|' . AuthServiceProvider::ROLE_COMPANY, 'scope:' . AuthServiceProvider::MANAGE_TEAMS]],
    function () use ($storeDestroyOnly) {
        Route::apiResource('users', 'UserController', $storeDestroyOnly);
    }
);

Route::group(
    ['middleware' => ['auth:api', 'role:' . $companyUsers, 'scope:' . AuthServiceProvider::MANAGE_TEAMS]],
    function () {
        Route::put('teams/{team}/users/{user}', 'TeamController@addUser');
        Route::delete('teams/{team}/users/{user}', 'TeamController@removeUser');
    }
);
