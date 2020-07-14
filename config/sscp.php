<?php

use Carbon\Carbon;

return [

    /************************************************************************
     * CONTROLPAD CONFIGS
     ************************************************************************/
    'CP_DEV_BASE_PATH'=> env('CP_DEV_BASE_PATH'),
    'CP_BASE_PATH'=> env('CP_BASE_PATH'),

    /************************************************************************
     * SHIPSTATION CONFIGS
     ************************************************************************/
    'SS_BASE_PATH'=> env('SS_BASE_PATH'),

    /************************************************************************
     * API NOTIFICATION ROUTE
     ************************************************************************/
    'API_NOTIFICATIONS' => env('API_NOTIFICATIONS'),

    /************************************************************************
     * SSCP CONFIGS
     ************************************************************************/
    'SSCP_START_DATE' => Carbon::now()->subYears(2),
    'SSCP_END_DATE' => Carbon::now(),

    'TRACKING_URLS' => [
        'USPS' => 'https://tools.usps.com/go/TrackConfirmAction?qtc_tLabels1=',
        'UPS' => 'http://wwwapps.ups.com/WebTracking/track?track=yes&trackNums=',
        'FEDEX' => 'http://www.fedex.com/Tracking?tracknumbers='
    ],

];

