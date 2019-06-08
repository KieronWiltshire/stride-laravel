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
    Route::put('/{id}', 'UserController@update')->name('update')->where('id', '[0-9]+');
    Route::post('/{id}/email', 'UserController@requestEmailChange')->name('change_email')->where('id', '[0-9]+');
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
  Route::name('oauth.')->prefix('oauth')->group(function() {
    /**
     * Authorize
     */
    Route::namespace('OAuth')->middleware('auth')->group(function() {
      Route::get('/authorize', 'AuthorizationController@authorize')->name('authorizations.authorize');
      Route::post('/authorize', 'ApproveAuthorizationController@approve')->name('authorizations.approve');
      Route::delete('/authorize', 'DenyAuthorizationController@deny')->name('authorizations.deny');
    });

    /**
     * Token
     */
    Route::namespace('OAuth')->group(function() {
      Route::post('/token', 'AccessTokenController@issueToken')->name('token');
    });
    Route::namespace('\Laravel\Passport\Http\Controllers')->middleware('auth')->group(function() {
      Route::get('/tokens', 'AuthorizedAccessTokenController@forUser')->name('tokens.index');
      Route::delete('/tokens/{token_id}', 'AuthorizedAccessTokenController@destroy')->name('tokens.destroy');
    });
    Route::namespace('OAuth')->middleware('auth')->group(function() {
      Route::post('/token/refresh', 'TransientTokenController@refresh')->name('token.refresh');
    });

    /**
     * Scopes
     */
    Route::namespace('\Laravel\Passport\Http\Controllers')->middleware('auth')->group(function() {
      Route::get('/scopes', 'ScopeController@all')->name('scopes.index');
    });

    /**
     * Personal Access Tokens
     */
    Route::namespace('OAuth')->middleware('auth')->group(function() {
      Route::get('/personal-access-tokens', 'PersonalAccessTokenController@forUser')->name('personal.tokens.index');
      Route::post('/personal-access-tokens', 'PersonalAccessTokenController@store')->name('personal.tokens.store');
      Route::delete('/personal-access-tokens/{token_id}', 'PersonalAccessTokenController@destroy')->name('personal.tokens.destroy');
    });

    /**
     * Clients
     */
    Route::namespace('OAuth')->middleware('auth')->group(function() {
      Route::get('/clients', 'ClientController@forUser')->name('clients.index');
      Route::post('/clients', 'ClientController@store')->name('clients.store');
      Route::put('/clients/{client_id}', 'ClientController@update')->name('clients.update');
      Route::delete('/clients/{client_id}', 'ClientController@destroy')->name('clients.destroy');
    });
  });

  Route::get('/test', function() {
    dd(request());
  });

  /**
   * 404 catch
   */
  Route::fallback(function(){
    throw new App\Exceptions\Router\UnableToLocateRequestRouteException();
  })->name('fallback.404');
});
