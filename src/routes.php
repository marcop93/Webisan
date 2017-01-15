<?php

Route::group(['prefix' => '/webisan'], function () {
    Route::get('/settings', '\Marcop93\Webisan\WebisanController@settings');
    Route::post('/settings', '\Marcop93\Webisan\WebisanController@settingsSave');
    Route::post('/command/{class}', '\Marcop93\Webisan\WebisanController@run');
    Route::get('/{option?}/{search?}', '\Marcop93\Webisan\WebisanController@show');
    Route::post('/search', '\Marcop93\Webisan\WebisanController@search');
});
