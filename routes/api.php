<?php

use Illuminate\Http\Request;

/***************************************************************************
 * PUBLIC ROUTES
 ***************************************************************************/
Route::post('api/shipstation/notify-shipped', 'ShipStationController@notifyShipped');

/***************************************************************************
 * DIAGNOSTIC REPORTS
 ***************************************************************************/
Route::post('shipstation/report', 'ShipStationController@report');
Route::post('controlpad/report', 'ControlPadController@report');

