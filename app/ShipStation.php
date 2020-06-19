<?php


namespace App;

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
        return env('SHIPSTATION_API_KEY') . ":" . env('SHIPSTATION_API_SECRET');
    }

    public function encryptRawAuthenticationToken(string $rawToken)
    {
        return base64_encode($rawToken);
    }

    public function generateAuthorizationHeader(string $encryptedToken)
    {
        return 'Authorization: Basic ' . $encryptedToken;
    }

}
