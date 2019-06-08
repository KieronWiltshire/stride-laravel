<?php

/*
|--------------------------------------------------------------------------
| OAuth Routes
|--------------------------------------------------------------------------
|
| Here is where you can register OAuth routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "oauth" middleware group.
|
*/

Route::namespace('OAuth')->name('oauth.')->group(function () {
  /**
   * Laravel Passport (OAuth)
   */

  /**
   * Token
   */
  Route::post('/token', 'AccessTokenController@issueToken')->name('token');

  /**
   * Clients
   */
  Route::middleware('auth')->group(function () {
    /**
     * Authorize
     */
    Route::get('/authorize', 'AuthorizationController@authorize')->name('authorizations.authorize');
    Route::post('/authorize', 'ApproveAuthorizationController@approve')->name('authorizations.approve');
    Route::delete('/authorize', 'DenyAuthorizationController@deny')->name('authorizations.deny');

    /**
     * Token
     */
    Route::get('/tokens', 'AuthorizedAccessTokenController@forUser')->name('tokens.index');
    Route::delete('/tokens/{token_id}', 'AuthorizedAccessTokenController@destroy')->name('tokens.destroy');

    /**
     * Scopes
     */
    Route::get('/scopes', 'ScopeController@all')->name('scopes.index');

    /**
     * Personal Access Tokens
     */
    Route::get('/personal-access-tokens', 'PersonalAccessTokenController@forUser')->name('personal.tokens.index');
    Route::post('/personal-access-tokens', 'PersonalAccessTokenController@store')->name('personal.tokens.store');
    Route::delete('/personal-access-tokens/{token_id}', 'PersonalAccessTokenController@destroy')->name('personal.tokens.destroy');

    /**
     * Clients
     */
    Route::get('/clients', 'ClientController@forUser')->name('clients.index');
    Route::post('/clients', 'ClientController@store')->name('clients.store');
    Route::put('/clients/{client_id}', 'ClientController@update')->name('clients.update');
    Route::delete('/clients/{client_id}', 'ClientController@destroy')->name('clients.destroy');
  });

  /**
   * 404 catch
   */
  Route::fallback(function () {
    throw new App\Exceptions\Router\UnableToLocateRequestRouteException();
  })->name('fallback.404');
});
