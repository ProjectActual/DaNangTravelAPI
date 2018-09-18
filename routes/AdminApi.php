<?php

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'Auth\\'], function () {
    // Route::post('login', 'AuthController@login')->name('login');
    Route::post('create', 'PasswordResetController@create')->name('create');
    Route::get('authenticate-token/{token}', 'PasswordResetController@authenticateToken')->name('authenticate_token');
    Route::put('reset', 'PasswordResetController@reset')->name('reset');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('logout', 'AuthController@logout')->name('logout');
        Route::get('user', 'AuthController@user')->name('user');

        Route::post('change-password', 'AuthController@changePassword')->name('change_password');
    });
});

Route::group(['middleware' => ['admin', 'auth:api']], function () {

    Route::group(['prefix' => 'posts', 'as' => 'posts.'], function () {
        Route::get('/', 'PostController@index')->name('index');
        Route::get('/show/{id}', 'PostController@show')->name('show');

        Route::post('create', 'PostController@store')->name('store');

        Route::put('update/{id}', 'PostController@update')->name('update');

        Route::delete('destroy/{id}', 'PostController@destroy')->name('destroy');
    });

    Route::group(['prefix' => 'categories', 'as' => 'categories.'], function () {
        Route::get('/', 'CategoryController@index')->name('index');

        Route::post('/create', 'CategoryController@store')->name('store');

        Route::put('/update/{id}', 'CategoryController@update')->name('update');
    });

    Route::group(['prefix' => 'tags', 'as' => 'tags.'], function () {
        Route::get('/', 'TagController@index')->name('index');

        Route::put('/update/{id}', 'TagController@update')->name('update');

        Route::delete('destroy/{id}', 'TagController@destroy')->name('destroy');
    });
});

