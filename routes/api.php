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
    Route::namespace('OAuth')->group(function() {
      Route::get('/authorize', 'AuthorizationController@authorize')->name('authorizations.authorize');
      Route::post('/authorize', 'ApproveAuthorizationController@approve')->name('authorizations.approve');
    });
    Route::namespace('\Laravel\Passport\Http\Controllers')->group(function() {
      Route::delete('/authorize', 'DenyAuthorizationController@deny')->name('authorizations.deny');
    });

    /**
     * Token
     */
    Route::namespace('OAuth')->group(function() {
      Route::post('/token', 'AccessTokenController@issueToken')->name('token');
    });
    Route::namespace('\Laravel\Passport\Http\Controllers')->group(function() {
      Route::get('/tokens', 'AuthorizedAccessTokenController@forUser')->name('tokens.index')->middleware('auth');
      Route::delete('/tokens/{token_id}', 'AuthorizedAccessTokenController@destroy')->name('tokens.destroy')->middleware('auth');
    });

    /**
     * Clients
     */
    Route::namespace('\Laravel\Passport\Http\Controllers')->group(function() {
      Route::get('/clients', 'ClientController@forUser')->name('clients.index')->middleware('auth');
      Route::post('/clients', 'ClientController@store')->name('clients.store')->middleware('auth');
      Route::put('/clients/{client_id}', 'ClientController@update')->name('clients.update')->middleware('auth');
      Route::delete('/clients/{client_id}', 'ClientController@destroy')->name('clients.destroy')->middleware('auth');
    });
  });

  /**
   * 404 catch
   */
  Route::fallback(function(){
    throw new App\Exceptions\Router\UnableToLocateRequestRouteException();
  })->name('fallback.404');
});
