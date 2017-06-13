<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'Api',], function () 
{
    Route::post('login', 'UsersController@login')->name('api.login');

    Route::post('signup/', 'UsersController@signup')->name('api.signup');

    Route::post('forgot-password/', 'UsersController@forgotPassword')->name('api.forgot-password');
    
    Route::get('campus/', 'APICampusController@index')->name('campus.index');
    Route::get('interests/', 'APIInterestsController@index')->name('interest.index');
});

Route::group(['namespace' => 'Api', 'middleware' => 'jwt.customauth'], function () 
{
    Route::get('events', 'APIEventsController@index')->name('events.index');
    Route::post('events/create', 'APIEventsController@create')->name('events.create');
    Route::post('events/edit', 'APIEventsController@edit')->name('events.edit');
    Route::post('events/delete', 'APIEventsController@delete')->name('events.delete');


    Route::get('user-profile/{id}', 'APIUserController@profile')->name('api-user.profile');
    Route::post('user-profile/update-profile', 'APIUserController@updateProfile')->name('api-user.update-profile');
});