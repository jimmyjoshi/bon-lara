<?php

Route::group([
    'namespace'  => 'Interest',
], function () {
	
	/*
     * Admin Event Controller
     */
    Route::resource('interests', 'AdminInterestsController',[
    	'except' => ['show']
    ]);
    
    Route::get('interests/', 'AdminInterestsController@index')->name('interests.index');
    Route::get('interests/get', 'AdminInterestsController@getTableData')->name('interests.get-list-data');
});
