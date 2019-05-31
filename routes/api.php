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
    Route::get('/me', 'AuthController@me')->name('me')->middleware('auth:api');
    Route::post('/logout', 'AuthController@logout')->name('logout')->middleware('auth:api');
    Route::post('/refresh', 'AuthController@refresh')->name('refresh')->middleware('auth:api');

    /**
     * Laravel Passport
     */
    Route::namespace('\Laravel\Passport\Http\Controllers')->group(function() {
      Route::get('/authorize', 'AuthorizationController@authorize')->name('authorizations.authorize');
      Route::post('/authorize', 'ApproveAuthorizationController@approve')->name('authorizations.approve');
      Route::delete('/authorize', 'DenyAuthorizationController@deny')->name('authorizations.deny');

      Route::post('/token', 'AccessTokenController@issueToken')->name('token')->middleware('throttle');
      Route::get('/tokens', 'AuthorizedAccessTokenController@forUser')->name('tokens.index')->middleware('auth:api');
      Route::delete('/tokens/{token_id}', 'AuthorizedAccessTokenController@destroy')->name('tokens.destroy')->middleware('auth:api');

      Route::get('/clients', 'ClientController@forUser')->name('clients.index');
      Route::post('/clients', 'ClientController@store')->name('clients.store');
      Route::put('/clients/{client_id}', 'ClientController@update')->name('clients.update');
      Route::delete('/clients/{client_id}', 'ClientController@destroy')->name('passport.clients.destroy');
    });
  });

  /**
   * 404 catch
   */
  Route::fallback(function(){
    throw (new App\Exceptions\Router\UnableToLocateRequestRouteException())->setContext([
      'route' => __('route-error.route_not_found')
    ]);
  })->name('fallback.404');
});
