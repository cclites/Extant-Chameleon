<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'shippingeasy'], function()
{
    // Webhooks
    Route::post('webhooks/{client}/notify-shipped', 'ShippingEasyController@notifyShipped');
});

