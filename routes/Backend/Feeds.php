<?php

Route::group([
    'namespace'  => 'Feeds',
], function () {

    /*
     * Admin Feeds Controller
     */
    Route::resource('feeds', 'AdminFeedsController',[
    	'except' => ['show']
    ]);

    Route::get('feeds/', 'AdminFeedsController@index')->name('feeds.index');
    Route::get('feeds/get', 'AdminFeedsController@getTableData')->name('feeds.get-list-data');
});
