<?php

use App\Providers\AuthServiceProvider;

Auth::routes(['verify' => true]);
Route::get('/', function () {
    return redirect('admin');
});
Route::get('/home', 'HomeController@index')->middleware('verified');

// OAuth
//Route::get('callback', 'Oauth2Controller@callback');
//Route::get('/', function () {
//    $queryParams = [
//        'client_id' => '3',
//        'redirect_uri' => url('callback'),
//        'response_type' => 'code',
//        'scope' => '',
//    ];
//    return redirect(route('passport.authorizations.authorize', $queryParams));
//});
//Route::get('/create-role', function () {
//    $user = \App\Models\User::first();
//    $user->attachRole('admin');
//    dd($user);
//});

// Admin dashboard
Route::get('/admin', function () {
    return view('auth.login');
});

Route::group(['prefix' => 'admin', 'middleware' => 'auth', 'middleware' => ['role:' . AuthServiceProvider::ROLE_ADMINISTRATOR], 'namespace' => 'Admin'], function () {
    Route::resource('users', 'UsersController');
    Route::resource('permission', 'PermissionController');
    Route::resource('roles', 'RolesController');
});
Route::group(['prefix' => 'admin', 'middleware' => 'auth', 'middleware' => ['role:' . AuthServiceProvider::ROLE_CLIENT_APP], 'namespace' => 'Admin'], function () {
    Route::get('oauth-dashboard', 'Oauth2Controller@index')->name('oauth-dashboard');
});