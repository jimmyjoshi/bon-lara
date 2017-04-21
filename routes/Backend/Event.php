<?php

Route::group([
    'prefix'     => 'event',
    'namespace'  => 'Event',
], function () {

    /*
     * Admin Event Controller
     */
    Route::resource('event', 'AdminEventController');

    Route::get('/', 'AdminEventController@index')->name('event.index');
    Route::get('/get', 'AdminEventController@getTableData')->name('event.get-event-data');
});
