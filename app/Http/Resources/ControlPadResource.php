<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class ControlPadResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            //TODO - map $request to a ControlPad order
        ];
    }
}

/*
 *     "data": [
        {
            "id": 7,
            "receipt_id": "SQG5QA-7",
            "type_id": 3,
            "type_description": "Rep to Customer",
            "buyer_first_name": "Juliet",
            "buyer_last_name": "Goodwin",
            "buyer_email": "schultz.tomas@example.org",
            "seller_id": 106,
            "buyer_id": 2015,
            "total_price": 50.23,
            "subtotal_price": 34.63,
            "total_discount": 0,
            "total_shipping": 13,
            "total_tax": 2.6,
            "cash": 0,
            "source": "ios",
            "paid_at": null,
            "status": "fulfilled",
            "created_at": "2018-01-08 21:38:59",
            "updated_at": "2018-02-08 20:00:45",
            "billing_address": {
                "name": "Juliet Goodwin",
                "line_1": "6777 Padberg Corner",
                "line_2": "Apt. 377",
                "city": "New Roger",
                "zip": "09310-0933",
                "state": "HI"
            },
            "shipping_address": {
                "name": "Juliet Goodwin",
                "line_1": "6777 Padberg Corner",
                "line_2": "Apt. 377",
                "city": "New Roger",
                "zip": "09310-0933",
                "state": "HI"
            }
        }
    ],
 */
