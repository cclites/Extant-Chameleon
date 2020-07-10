<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'shipstation'], function()
{
    // Webhooks
    Route::post('webhooks/{client}/notify-shipped', 'ShipStationController@notifyShipped');
});

/***************************************************************************
 * DIAGNOSTIC REPORTS - unused
 ***************************************************************************/
//Route::post('api/shipstation/report', 'ShipStationController@report');
//Route::post('api/controlpad/report', 'ControlPadController@report');
