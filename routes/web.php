<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('login', 'LoginController@index')->name('login');
Route::post('login', 'LoginController@attemptLogin');

Route::group(['middleware' => 'auth', 'prefix' => ''], function () {
    Route::group(['prefix' => ''], function () {
        Route::get('/', 'MainController@index')->name('main');
        Route::get('search', 'MainController@search')->name('search');
        Route::post('addOwner', 'MainController@addOwner')->name('addOwner');
        Route::post('addLocation', 'MainController@addLocation')->name('addLocation');
        Route::get('history', 'MainController@history')->name('history');
        Route::get('getOwners', 'MainController@getOwners');
        Route::get('getLocations', 'MainController@getLocations');
        Route::get('logOut', 'LoginController@logOut')->name('logOut');
    });

    Route::group(['prefix' => 'group'], function () {
        Route::get('show', 'GroupController@showGroupItems')->name('groupShow');
        Route::post('untieItem', 'GroupController@untieItem')->name('untieItem');
        Route::post('reTieItem', 'GroupController@reTieItem')->name('reTieItem');
        Route::post('disband', 'GroupController@disband')->name('disband');
        Route::post('create', 'GroupController@create')->name('createGroup');
    });

    Route::group(['prefix' => 'edit'], function () {
        Route::get('/', 'EditController@edit')->name('edit');
        Route::post('save', 'EditController@save')->name('save');
        Route::post('saveNewLocation', 'EditController@saveNewLocation')->name('saveNewLocation');
    });

    Route::group(['prefix' => 'documents'], function () {
        Route::get('/', 'DocumentsController@main')->name('DocMain');
        Route::post('save', 'DocumentsController@save')->name('DocSave');
        Route::get('getDocuments', 'DocumentsController@getDocuments')->name('getDocuments');
        Route::get('showMore', 'DocumentsController@showMore')->name('showMore');
        Route::post('accept', 'DocumentsController@accept')->name('accept');
        Route::post('reject', 'DocumentsController@reject')->name('reject');
        Route::post('saveDocumentFile', 'DocumentsController@saveDocumentFile')->name('saveDocumentFile');
        Route::get('create', 'DocumentsController@create')->name('docCreate');
        Route::get('print', 'DocumentsController@printDoc');
        Route::post('saveReasons', 'DocumentsController@saveReasons');
    });
});