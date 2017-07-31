<?php

Route::group([
    'namespace'  => 'Group',
], function () {

    /*
     * Admin Group Controller
     */
    Route::resource('group', 'AdminGroupController');

    Route::get('groups/', 'AdminGroupController@index')->name('group.index');
    Route::get('/get', 'AdminGroupController@getTableData')->name('group.get-list-data');
	
	//Route::get('groups/get-group-members', 'AdminGroupController@getGroupMembers')->name('group.get-group-members');
});

