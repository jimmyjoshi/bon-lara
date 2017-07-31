<?php

Route::group([
    'namespace'  => 'Event',
], function () {

    /*
     * Admin Event Controller
     */
    Route::resource('event', 'AdminEventController');

    Route::get('event/', 'AdminEventController@index')->name('event.index');
    Route::get('event/get-list-data', 'AdminEventController@getTableData')->name('event.get-list-data');
});
