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
   * User routes
   */
  Route::name('user.')->prefix('user')->group(function () {
    Route::get('/', 'UserController@index')->name('index');
    Route::post('/', 'UserController@create')->name('create');
    Route::get('/search', 'UserController@search')->name('search');
    Route::get('/{id}', 'UserController@getById')->name('get')->where('id', '[0-9]+');
    Route::put('/{id}', 'UserController@update')->name('update')->where('id', '[0-9]+')->middleware('auth');
    Route::post('/{id}/email', 'UserController@requestEmailChange')->name('change_email')->where('id', '[0-9]+')->middleware('auth');
    Route::get('/{email}', 'UserController@getByEmail')->name('get');
    Route::get('/{email}/verify', 'UserController@verifyEmail')->name('verify_email');
    Route::post('/{email}/resend', 'UserController@resendEmailVerificationToken')->name('resend_email_verification_token');
    Route::post('/{email}/forgot', 'UserController@forgotPassword')->name('forgot_password');
    Route::post('/{email}/reset', 'UserController@resetPassword')->name('reset_password');
  });

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
      Route::get('/user/me/personal-access-tokens', 'PersonalAccessTokenController@forAuthenticatedUser')->name('index');
      Route::get('/user/{id}/personal-access-tokens', 'PersonalAccessTokenController@forUser')->where('id', '[0-9]+')->name('index');
      Route::post('/personal-access-tokens', 'PersonalAccessTokenController@store')->name('store');
      Route::delete('/personal-access-tokens/{token_id}', 'PersonalAccessTokenController@destroy')->name('destroy');
    });

    /**
     * Clients
     */
    Route::name('clients.')->middleware('auth')->group(function() {
      Route::get('/user/me/clients', 'ClientController@forAuthenticatedUser')->name('index');
      Route::get('/user/{id}/clients', 'ClientController@forUser')->where('id', '[0-9]+')->name('index');
      Route::post('/clients', 'ClientController@store')->name('store');
      Route::put('/clients/{id}', 'ClientController@update')->name('update');
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
   * 404 catch
   */
  Route::fallback(function(){
    throw new App\Exceptions\Router\UnableToLocateRequestRouteException();
  })->name('fallback.404');

});
