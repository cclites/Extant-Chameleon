<?php

use Illuminate\Http\Request;

/***************************************************************************
 * PUBLIC ROUTES
 ***************************************************************************/

/***************************************************************************
 * DIAGNOSTIC REPORTS
 ***************************************************************************/
Route::post('api/shipstation/report', 'ShipStationController@report');
Route::post('api/controlpad/report', 'ControlPadController@report');

