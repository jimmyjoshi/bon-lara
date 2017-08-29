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

    Route::get('privacy-policy', 'APIUserController@privacyPolicy')->name('api-user.privacy-policy');
    Route::get('terms-conditions', 'APIUserController@termsConditions')->name('api-user.terms-conditions');
});

Route::group(['namespace' => 'Api', 'middleware' => 'jwt.customauth'], function () 
{
    Route::get('events/', 'APIEventsController@index')->name('events.index');
    Route::post('events/create', 'APIEventsController@create')->name('events.create');
    Route::post('events/edit', 'APIEventsController@edit')->name('events.edit');
    Route::post('events/delete', 'APIEventsController@delete')->name('events.delete');

    Route::post('get-group-events/', 'APIEventsController@getGroupEvents')->name('events.get-group-events');


    Route::post('events/join-event', 'APIEventsController@joinEvent')->name('events.join-event');
    Route::post('events/exit-event', 'APIEventsController@skipEvent')->name('events.exit-event');


    Route::get('user-profile/{id}', 'APIUserController@profile')->name('api-user.profile');
    
    Route::post('users/report-user', 'APIUserController@reportUser')->name('api-user.report-user');

    

    Route::post('set-user-token/', 'APIUserController@setToken')->name('api-user.set-token');
    Route::post('user-profile/update-profile', 'APIUserController@updateProfile')->name('api-user.update-profile');
    Route::get('campus-users/', 'APIUserController@getAllCampusUsers')->name('api-user.get-all-users');
    
    Route::get('user-profile-with-interest/{id}', 'APIUserController@profileWithInterest')->name('api-user.profile-with-interest');

    Route::post('user-interest/add-interest', 'APIUserController@addInterest')->name('api-user.add-interest');
    Route::post('user-interest/remove-interest', 'APIUserController@removeInterest')->name('api-user.remove-interest');

    Route::post('user-interest/remove-bulk-interest', 'APIUserController@removeBulkInterest')->name('api-user.remove-bulk-interest');

    Route::post('user-interest/add-bulk-interest', 'APIUserController@addBulkInterest')->name('api-user.add-bulk-interest');

    Route::get('groups/', 'APIGroupsController@index')->name('groups.index');
    Route::get('groups/random-groups', 'APIGroupsController@getRandomGroupsByCampusId')->name('groups.random-groups');
    
    Route::get('get-for-you-groups/', 'APIGroupsController@getForYouGroups')->name('groups.get-for-you-groups');
    Route::post('groups/create', 'APIGroupsController@create')->name('groups.create');
    Route::post('groups/edit', 'APIGroupsController@edit')->name('groups.edit');
    Route::post('groups/exit', 'APIGroupsController@exitGroup')->name('groups.exit');

    Route::post('groups/manage-permission', 'APIGroupsController@allowMemberPermissions')->name('groups.allow-member-permissions');
    

    Route::post('groups/add-member', 'APIGroupsController@joinMember')->name('groups.join-member');

    Route::post('groups/get-member-suggestions', 'APIGroupsController@getMemberSuggestions')->name('groups.get-member-suggestions');

    Route::post('groups/change-member-status', 'APIGroupsController@changeMemberStatus')->name('groups.change-member-status');


    Route::post('groups/remove-member', 'APIGroupsController@removeMember')->name('groups.remove-member');
    Route::post('groups/get-group-members', 'APIGroupsController@getAllGroupMembers')->name('groups.get-group-members');

    Route::get('get-channels/', 'APIChannelController@index')->name('channels.get-all-channels');
    Route::post('channels/get-channels-by-group-id/', 'APIChannelController@getChannelsByGroupId')->name('channels.get-channels-by-group-id');
    Route::post('create-new-channel/', 'APIChannelController@create')->name('channels.create-new-channel');
    Route::post('channels/delete-channel/', 'APIChannelController@deleteChannel')->name('channels.delete-channel');

    Route::get('get-all-feeds/', 'APIFeedsController@index')->name('feeds.get-all-feeds');
    Route::get('get-home-feeds/', 'APIFeedsController@getAllCampusFeeds')->name('feeds.get-home-feeds');
    
    Route::post('get-all-channel-feeds/', 'APIFeedsController@getChannelFeeds')->name('feeds.get-channel-feeds');
    Route::post('create-new-feed/', 'APIFeedsController@create')->name('feeds.create-new-feed');
    Route::post('delete-feed/', 'APIFeedsController@destroy')->name('feeds.delete-feed');
    Route::post('feeds/report', 'APIFeedsController@reportFeed')->name('feeds.report-feed');
});