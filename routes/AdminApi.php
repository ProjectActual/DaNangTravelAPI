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
    Route::post('/forget-password', 'PasswordResetController@create')->name('create');
    Route::post('/forget-password/{token}', 'PasswordResetController@authenticateToken')->name('authenticate_token');
    Route::put('/forget-password', 'PasswordResetController@reset')->name('reset');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('logout', 'AuthController@logout')->name('logout');
        Route::get('user', 'AuthController@user')->name('user');

        Route::post('change-password', 'AuthController@changePassword')->name('change_password');
    });
});

Route::group(['middleware' => ['admin', 'auth:api']], function () {

    Route::group(['prefix' => 'posts', 'as' => 'posts.'], function () {
        Route::get('/', 'PostController@index')->name('index');
        Route::get('/{id}', 'PostController@show')->name('show');

        Route::post('/uploadFile', 'PostController@uploadFile')->name('upload_file');

        Route::post('/', 'PostController@store')->name('store');

        Route::put('/{id}', 'PostController@update')->name('update');
        Route::post('/edit/{id}', 'PostController@edit')->name('edit');

        Route::delete('/{id}', 'PostController@destroy')->name('destroy');
    });

    Route::group(['prefix' => 'categories', 'as' => 'categories.'], function () {
        Route::get('/', 'CategoryController@index')->name('index');

        Route::post('/', 'CategoryController@store')->name('store');

        Route::put('/{id}', 'CategoryController@update')->name('update');
    });

    Route::group(['prefix' => 'tags', 'as' => 'tags.'], function () {
        Route::get('/', 'TagController@index')->name('index');

        Route::put('/{id}', 'TagController@update')->name('update');

        Route::delete('/{id}', 'TagController@destroy')->name('destroy');
    });
});

