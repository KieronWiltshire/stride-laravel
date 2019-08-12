<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace('Api')->name('api.')->group(function () {

  /**
   * Authentication routes
   */
  Route::name('auth.')->prefix('auth')->group(function() {
    Route::post('/login', 'AuthController@login')->name('login');
    Route::get('/me', 'AuthController@me')->name('me')->middleware('auth');
    Route::post('/logout', 'AuthController@logout')->name('logout')->middleware('auth');
  });

  /**
   * Laravel Passport (OAuth)
   */
  Route::namespace('OAuth')->middleware('oauth')->name('oauth.')->prefix('oauth')->group(function() {
    /**
     * Authorize
     */
    Route::name('authorizations.')->middleware('auth')->group(function () {
      Route::get('/authorize', 'AuthorizationController@authorize')->name('authorize');
      Route::post('/authorize', 'ApproveAuthorizationController@approve')->name('approve');
      Route::delete('/authorize', 'DenyAuthorizationController@deny')->name('deny');
    });

    /**
     * Scopes
     */
    Route::name('scopes.')->middleware('auth')->group(function () {
      Route::get('/scopes', '\Laravel\Passport\Http\Controllers\ScopeController@all')->name('index');
    });

    /**
     * Personal Access Tokens
     */
    Route::name('personal-access-tokens.')->middleware('auth')->group(function() {
      Route::get('/user/me/personal-access-tokens', 'PersonalAccessTokenController@forAuthenticatedUser')->name('get');
      Route::get('/user/{id}/personal-access-tokens', 'PersonalAccessTokenController@forUser')->where('id', '[0-9]+')->name('get');

      Route::post('/personal-access-tokens', 'PersonalAccessTokenController@store')->name('store');
      Route::delete('/personal-access-tokens/{token_id}', 'PersonalAccessTokenController@destroy')->name('destroy');
    });

    /**
     * Clients
     */
    Route::name('clients.')->middleware('auth')->group(function() {
      Route::get('/user/me/clients', 'ClientController@forAuthenticatedUser')->name('get');
      Route::get('/user/{id}/clients', 'ClientController@forUser')->where('id', '[0-9]+')->name('get');

      Route::post('/clients', 'ClientController@store')->name('store');
      Route::patch('/clients/{id}', 'ClientController@update')->name('update');
      Route::delete('/clients/{id}', 'ClientController@destroy')->name('destroy');
    });

    /**
     * Tokens
     */
    Route::name('tokens.')->middleware('auth')->group(function() {
      Route::get('/tokens', 'AuthorizedAccessTokenController@forUser')->name('index');
      Route::delete('/tokens/{token_id}', 'AuthorizedAccessTokenController@destroy')->name('destroy');
    });

    /**
     * Issue tokens (non authenticated)
     */
    Route::post('/token', 'AccessTokenController@issueToken')->name('token');

  });

  /**
   * User routes
   */
  Route::name('user.')->prefix('user')->group(function () {
    Route::get('/', 'UserController@index')->name('index');
    Route::post('/', 'UserController@create')->name('create');
    Route::get('/search', 'UserController@search')->name('search');
    Route::get('/{id}', 'UserController@getById')->name('get')->where('id', '[0-9]+');
    Route::patch('/{id}', 'UserController@update')->name('update')->where('id', '[0-9]+')->middleware('auth');
    Route::post('/{id}/email', 'UserController@requestEmailChange')->name('change_email')->where('id', '[0-9]+')->middleware('auth');
    Route::get('/{email}', 'UserController@getByEmail')->name('get');
    Route::get('/{email}/verify', 'UserController@verifyEmail')->name('verify_email');
    Route::post('/{email}/resend', 'UserController@resendEmailVerificationToken')->name('resend_email_verification_token');
    Route::post('/{email}/forgot', 'UserController@forgotPassword')->name('forgot_password');
    Route::post('/{email}/reset', 'UserController@resetPassword')->name('reset_password');

    Route::patch('/{id}/assign-role/{roleId}', 'UserController@assignRole')->name('assign-role')->where('id', '[0-9]+')->where('roleId', '[0-9]+')->middleware('auth');
    Route::patch('/{id}/assign-roles', 'UserController@assignRoles')->name('assign-roles')->where('id', '[0-9]+')->middleware('auth');
    Route::patch('/{id}/deny-role/{roleId}', 'UserController@denyRole')->name('deny-role')->where('id', '[0-9]+')->where('roleId', '[0-9]+')->middleware('auth');
    Route::patch('/{id}/deny-roles', 'UserController@denyRoles')->name('deny-roles')->where('id', '[0-9]+')->middleware('auth');

    Route::patch('/{id}/assign-permission/{permissionId}', 'UserController@assignPermission')->name('assign-permission')->where('id', '[0-9]+')->where('permissionId', '[0-9]+')->middleware('auth');
    Route::patch('/{id}/assign-permissions', 'UserController@assignPermissions')->name('assign-permissions')->where('id', '[0-9]+')->middleware('auth');
    Route::patch('/{id}/deny-permission/{permissionId}', 'UserController@denyPermission')->name('deny-permission')->where('id', '[0-9]+')->where('permissionId', '[0-9]+')->middleware('auth');
    Route::patch('/{id}/deny-permissions', 'UserController@denyPermissions')->name('deny-permissions')->where('id', '[0-9]+')->middleware('auth');
  });

  /**
   * Role routes
   */
  Route::name('role.')->prefix('role')->group(function () {
    Route::get('/', 'RoleController@index')->name('index');
    Route::post('/', 'RoleController@create')->name('create')->middleware('auth');
    Route::get('/search', 'RoleController@search')->name('search');
    Route::get('/{id}', 'RoleController@getById')->name('get')->where('id', '[0-9]+');
    Route::patch('/{id}', 'RoleController@update')->name('update')->where('id', '[0-9]+')->middleware('auth');
    Route::get('/{name}', 'RoleController@getByName')->name('get');

    Route::patch('/{id}/assign-permission/{permissionId}', 'RoleController@assignPermission')->name('assign-permission')->where('id', '[0-9]+')->where('permissionId', '[0-9]+')->middleware('auth');
    Route::patch('/{id}/assign-permissions', 'RoleController@assignPermissions')->name('assign-permissions')->where('id', '[0-9]+')->middleware('auth');
    Route::patch('/{id}/deny-permission/{permissionId}', 'RoleController@denyPermission')->name('deny-permission')->where('id', '[0-9]+')->where('permissionId', '[0-9]+')->middleware('auth');
    Route::patch('/{id}/deny-permissions', 'RoleController@denyPermissions')->name('deny-permissions')->where('id', '[0-9]+')->middleware('auth');
  });

  /**
   * Permission routes
   */
  Route::name('permission.')->prefix('permission')->group(function () {
    Route::get('/', 'PermissionController@index')->name('index');
    Route::post('/', 'PermissionController@create')->name('create')->middleware('auth');
    Route::get('/search', 'PermissionController@search')->name('search');
    Route::get('/{id}', 'PermissionController@getById')->name('get')->where('id', '[0-9]+');
    Route::patch('/{id}', 'PermissionController@update')->name('update')->where('id', '[0-9]+')->middleware('auth');
    Route::get('/{name}', 'PermissionController@getByName')->name('get');
  });

  /**
   * 404 catch
   */
  Route::fallback(function(){
    throw new App\Exceptions\Router\UnableToLocateRequestRouteException();
  })->name('fallback.404');

});
