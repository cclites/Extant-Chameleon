<?php

namespace App\Transformers;


class CpOrderItemToSsOrderItemTransformer
{
    public static function transform($orderItem): array
    {
        return [
            'lineItemKey' => $orderItem['id'],
            'sku' => $orderItem['manufacturer_sku'],
            'name' => $orderItem['name'],
            'quantity' => $orderItem['quantity'],
            'unitPrice' => $orderItem['price'],
            'createDate' => $orderItem['created_at'],
        ];
    }
}
