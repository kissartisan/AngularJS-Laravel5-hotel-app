<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {

	Route::group(['prefix' => 'api'], function() {
		Route::resource('authenticate', 'AuthenticateController', ['only' => ['index']]);
		Route::post('authenticate', 'AuthenticateController@authenticate');
		Route::post('searchavailability', 'RoomCalendarController@searchAvailability');
		Route::post('createreservation', 'ReservationController@createReservation');
	});

    Route::group(['prefix' => 'adminapi'], function() {
    	Route::resource('room_type', 'RoomTypeController');
    	Route::post('setpriceinrange', 'RoomCalendarController@setPriceInRangeForRoomType');
    });

});
