<?php

namespace App;

use Illuminate\Support\Facades\Auth;

/**
 * Class ShipStation
 * @package App
 *
 * NOTE: ShipStation is not a model representing a collection,
 *       but rather a representation of a ShipStation order. This file is used to
 *       define relationships between ControlPad orders and ShipStation orders
 */
class ShipStation
{
    protected $header;

    /****************************************************
     * RELATIONSHIPS
     ****************************************************/
    public function hasControlPadOrder($order_id)
    {
        //TODO: Call get function in ControlPadDataModel
    }

    /****************************************************
     * MUTATORS
     ****************************************************/
    public function generateRawAuthenticationToken()
    {
        if(env('APP_DEBUG') === true){
            return config('sscp.SS_DEV_PUBLIC_KEY') . ":" . config('sscp.SS_DEV_PRIVATE_KEY');
        }else{
            return "";
            //TODO:: Figure out where user credentials are coming from.
            //return Auth::user()->ss_public_key . ':' . Auth::user()->ss_private_key;
        }
    }

    public function encryptRawAuthenticationToken(string $rawToken)
    {
        return base64_encode($rawToken);
    }

    public function generateAuthorizationToken()
    {
        return $this->encryptRawAuthenticationToken($this->generateRawAuthenticationToken());
    }

    public function buildHeader()
    {
        return [
            'Authorization' => 'Basic ' . $this->generateAuthorizationToken(),
            'Content-Type' => 'application/json'
        ];
    }

    /****************************************************
     * ACCESSORS
     ****************************************************/
    public function getHeader()
    {
        return $this->buildHeader();
        //return $this->header;
    }

    /***************************************************
     * VALIDATION
     ***************************************************/
    public function rules(){}

}
