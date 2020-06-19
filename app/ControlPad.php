<?php


namespace App;


/**
 * Class ControlPad
 * @package App
 *
 * NOTE: ControlPad is not a model representing a collection,
 *       but rather a representation of a ControlPad order. This file is used to
 *       define relationships between ControlPad orders and ShipStation orders
 */
class ControlPad
{
    /****************************************************
     * RELATIONSHIPS
     ****************************************************/

    public function hasShipStationOrder($order_id)
    {
        //TODO: Call get function in ShipStationDataModel
    }
}
