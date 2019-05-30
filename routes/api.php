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

  Route::fallback(function(){
    throw (new App\Exceptions\Router\UnableToLocateRequestRouteException())->setContext([
      'route' => __('route-error.route_not_found')
    ]);
  })->name('fallback.404');
});
