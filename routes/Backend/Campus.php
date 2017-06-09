<?php

Route::group([
    'namespace'  => 'Campus',
], function () {

    /*
     * Admin Campus Controller
     */
    Route::resource('campus', 'AdminCampusController',[
    	'except' => ['show']
    ]);

    Route::get('campus/', 'AdminCampusController@index')->name('campus.index');
    Route::get('campus/get', 'AdminCampusController@getTableData')->name('campus.get-list-data');
});
