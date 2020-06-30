<?php


namespace App;


/**
 * Class ControlPad
 * @package App
 *
 * NOTE: ControlPad is not a DB model representing a collection,
 *       but rather a representation of a ControlPad order. This file is used to
 *       define relationships between ControlPad orders and ShipStation orders
 */
class ControlPad
{
    const UNFULFILLED_STATUS = 'unfulfilled';
    const DEFAULT_STATUS = self::UNFULFILLED_STATUS;
    const PENDING_STATUS = 'pending';
    const FULFILLED_STATUS = 'fulfilled';

    const STATUSES = [
        self::DEFAULT_STATUS,
        self::UNFULFILLED_STATUS,
        self::PENDING_STATUS,
        self::FULFILLED_STATUS
    ];

    /****************************************************
     * RELATIONSHIPS
     ****************************************************/


    /***************************************************
     * VALIDATION
     ***************************************************/
    public function rules(){}


    public function getHeader()
    {
        if(env('APP_DEBUG') === true){
            $apiKey = config('sscp.CP_DEV_API_KEY');
        }else{
            $apiKey = config('sscp.CP_API_KEY');
        }

        return [
            'Content-Type' => 'application/json',
            'APIKey' => $apiKey
        ];
    }

}
