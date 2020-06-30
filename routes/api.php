<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'api', 'prefix' => 'api'], function() {

    Route::post('shipstation/notify-shipped', 'ShipStationController@notifyShipped');

    if(env('APP_DEBUG')){
        Route::get('shipstation/notify-shipped', 'ShipStationController@testConnection');
    }

});

/***************************************************************************
 * DIAGNOSTIC REPORTS - unused
 ***************************************************************************/
//Route::post('api/shipstation/report', 'ShipStationController@report');
//Route::post('api/controlpad/report', 'ControlPadController@report');

