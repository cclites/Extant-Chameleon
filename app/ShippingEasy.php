<?php

namespace App;

use Illuminate\Support\Facades\Auth;

/**
 * Class ShippingEasy
 * @package App
 *
 * NOTE: ShippingEasy is not a model representing a collection,
 *       but rather a representation of a ShippingEasy order. This file is used to
 *       define relationships between ControlPad orders and ShippingEasy orders
 */
class ShippingEasy
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

    public function generateAuthorizationToken()
    {
        return '';
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
    public static $readRules = [
        'title' => 'required|unique:posts|max:255',
        'body' => 'required',
    ];

}
