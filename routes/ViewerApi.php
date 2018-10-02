<?php

Route::group(['as' => 'home.'], function () {
    Route::get('/', 'HomeController@index')->name('index');

    Route::get('/master', 'HomeController@master')->name('master');
});

Route::group(['as' => 'post.', 'prefix' => '{uri_category}'], function () {
    Route::get('/', 'PostController@index')->name('index');
});
