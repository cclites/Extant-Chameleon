<?php

/***************************************************************************
 * PUBLIC ROUTES
 ***************************************************************************/
Route::post('api/shipstation/notify-shipped', 'ShipStationController@notifyShipped');

/***************************************************************************
 * DIAGNOSTIC REPORTS
 ***************************************************************************/
Route::post('api/shipstation/report', 'ShipStationController@report');
Route::post('api/controlpad/report', 'ControlPadController@report');

