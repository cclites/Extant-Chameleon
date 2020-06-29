<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Illuminate\Http\Request;

/***************************************************************************
 * PUBLIC ROUTES
 ***************************************************************************/
Route::post('shipstation/notify-shipped', 'ShipStationController@notifyShipped');
Route::get('shipstation/notify-shipped', 'ShipStationController@testConnection');
