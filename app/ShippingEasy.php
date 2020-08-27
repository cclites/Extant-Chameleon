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

    /****************************************************
     * ACCESSORS
     ****************************************************/

    /***************************************************
     * VALIDATION
     ***************************************************/

}
