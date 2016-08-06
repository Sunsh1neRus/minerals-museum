<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::auth();

Route::group(['prefix' => 'minerals'], function () {
    // index
    Route::get('/', 'MineralsController@index');
    // show single
    Route::get('/{id}', 'MineralsController@show')->where('id', '[0-9]+');;
    // add new
    Route::get('/create', 'MineralsController@getCreate');
    Route::post('/create', 'MineralsController@postCreate');
    // update
    Route::get('/{id}/update', 'MineralsController@getUpdate')->where('id', '[0-9]+');
    Route::post('/{id}/update', 'MineralsController@postUpdate')->where('id', '[0-9]+');

    Route::post('/autocomplete', 'MineralsController@postAutocomplete');
});
Route::group(['prefix' => 'admin'], function () {
    Route::get('/', 'AdminController@index');
    Route::get('/users', 'AdminController@getUsersIndex');
    Route::post('/users/change-user-role', 'AdminController@postChangeUserRole');
    Route::post('/users/delete-user', 'AdminController@postDeleteUser');
});

Route::group(['prefix' => 'images'], function () {
    // upload
    Route::post('/upload-mineral-image', 'ImagesController@postUploadMineralImage');
    Route::post('/delete-mineral-image', 'ImagesController@postDeleteMineralImage');
    Route::post('/update-mineral-image-description', 'ImagesController@postUpdateMineralImageDescription');
    Route::post('/set-mineral-main-image', 'ImagesController@postSetMineralMainImage');
});