<?php
Route::get('/search', 'PostController@search')->name('search');

Route::group(['as' => 'home.'], function () {
    Route::get('/', 'HomeController@index')->name('index');

    Route::get('/master', 'HomeController@master')->name('master');
});

Route::group(['as' => 'posts.', 'prefix' => 'posts/{uri_category}'], function () {
    Route::get('/', 'PostController@index')->name('index');
    Route::get('/{uri_post}', 'PostController@show')->name('show');
});

Route::group(['as' => 'tags.', 'prefix' => 'tag/{uri_tag}'], function () {
    Route::get('/', 'TagController@index')->name('index');
});
