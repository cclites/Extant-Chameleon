<?php

use Carbon\Carbon;

return [

    /************************************************************************
     * CONTROLPAD CONFIGS
     ************************************************************************/
    'CP_DEV_BASE_PATH'=> env('CP_DEV_BASE_PATH'),
    'CP_BASE_PATH'=> env('CP_BASE_PATH'),

    'CP_ORDERS_START' => Carbon::now()->subMinutes(15),
    'CP_ORDERS_END' => Carbon::now(),

    /************************************************************************
     * SHIPSTATION CONFIGS
     ************************************************************************/
    'SS_BASE_PATH'=> env('SS_BASE_PATH'),
    'SHIPSTATION_API_NOTIFICATIONS' => env('SHIPSTATION_API_NOTIFICATIONS'),

    'SHIPSTATION_TRACKING_URLS' => [
        'USPS' => 'https://tools.usps.com/go/TrackConfirmAction?qtc_tLabels1=',
        'UPS' => 'http://wwwapps.ups.com/WebTracking/track?track=yes&trackNums=',
        'FEDEX' => 'http://www.fedex.com/Tracking?tracknumbers='
    ],

    /************************************************************************
     * SHIPPINGEASY CONFIGS
     ************************************************************************/
    'SS_BASE_PATH'=> env('SE_BASE_PATH'),
    'SHIPPINGEASY_API_NOTIFICATIONS' => env('SHIPPINGEASY_API_NOTIFICATIONS'),
];

