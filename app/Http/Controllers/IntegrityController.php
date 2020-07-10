<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class IntegrityController extends BaseController
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * Heartbeat endpoint
     */
    public function ping(Request $request)
    {
        return response()->json(['app' => 'Shipping Integration']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * Checks that a client config is setup
     */
    public function validateClientConfig(Request $request, $client)
    {
        $clientConfig = config('auths.'.$client);
        return response()->json(['config_exists' => ($clientConfig !== null)]);
    }
}
