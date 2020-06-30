<?php

use Carbon\Carbon;

return [

    /************************************************************************
     * CONTROLPAD CONFIGS
     ************************************************************************/
    'CP_DEV_BASE_PATH'=> env('CP_DEV_BASE_PATH'),
    'CP_DEV_API_KEY' => env('CP_DEV_API_KEY'),

    'CP_BASE_PATH'=> env('CP_BASE_PATH'),
    'CP_API_KEY' => env('CP_API_KEY'),

    /************************************************************************
     * SHIPSTATION CONFIGS
     ************************************************************************/
    'SS_BASE_PATH'=> env('SS_BASE_PATH'),

    /************************************************************************
     * DEVELOPER CONFIGS
     ************************************************************************/
    'SS_DEV_PUBLIC_KEY' => env('SS_DEV_PUBLIC_KEY'),
    'SS_DEV_PRIVATE_KEY' => env('SS_DEV_PRIVATE_KEY'),

    /************************************************************************
     * API NOTIFICATION ROUTE
     ************************************************************************/
    'API_NOTIFICATIONS' => env('API_NOTIFICATIONS'),

    /************************************************************************
     * SSCP CONFIGS
     ************************************************************************/
    'SSCP_START_DATE' => Carbon::now()->subMinutes(30),
    'SSCP_END_DATE' => Carbon::now(),

];
