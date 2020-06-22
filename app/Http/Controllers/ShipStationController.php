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

    public function notifyShipped(Request $request){

    }
}
