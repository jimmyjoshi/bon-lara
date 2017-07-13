<?php

Route::group([
    'namespace'  => 'Channel',
], function () {

    /*
     * Admin Channel Controller
     */
    Route::resource('channel', 'AdminChannelController',[
    	'except' => ['show']
    ]);

    Route::get('channels/', 'AdminChannelController@index')->name('channel.index');
    Route::get('channels/get', 'AdminChannelController@getTableData')->name('channel.get-list-data');
});
