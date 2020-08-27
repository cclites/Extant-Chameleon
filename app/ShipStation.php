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
    const MAX_ORDERS_PER_CLIENT = 100;

    protected $header;

    public $authConfig;

    /****************************************************
     * RELATIONSHIPS
     ****************************************************/


    /****************************************************
     * MUTATORS
     ****************************************************/
    public function generateRawAuthenticationToken()
    {
        return $this->authConfig['ShipStationPublicKey'] . ":" . $this->authConfig['ShipStationPrivateKey'] ;
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
    public function getHeader($authConfig)
    {
        $this->authConfig = $authConfig;
        return $this->buildHeader();
    }

    /***************************************************
     * VALIDATION
     ***************************************************/


}
