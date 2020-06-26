<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShipStationController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //TODO: Is there already an API middleware?
        //$this->middleware('auth');
    }

    public function notifyShipped(Request $request)
    {
        //1. Format request so responses can be iterated

        //2. Get order from CP - update status

        //3. Create tracking order


        \Log::info("NOTIFY SHIPPED");
        \Log::info(json_encode($request->all()));

        return response()->json(['message' => 'Notify shipped']);
    }

    public function report(Request $request)
    {
        //query for CP records within a date range
    }
}
