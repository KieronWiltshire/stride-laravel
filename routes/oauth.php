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
  Route::middleware('auth')->group(function () {
    /**
     * Authorize
     */
    Route::get('/authorize', 'AuthorizationController@authorize')->name('authorizations.authorize');
    Route::post('/authorize', 'ApproveAuthorizationController@approve')->name('authorizations.approve');
    Route::delete('/authorize', 'DenyAuthorizationController@deny')->name('authorizations.deny');

    /**
     * Scopes
     */
    Route::get('/scopes', '\Laravel\Passport\Http\Controllers\ScopeController@all')->name('scopes.index');
  });

  /**
   * Issue tokens
   */
  Route::post('/token', 'AccessTokenController@issueToken')->name('token');

  /**
   * 404 catch
   *
   * Callback to the front-end, bypassing "web" routes
   */
  Route::fallback(function () {
    return view('app');
  });
});
