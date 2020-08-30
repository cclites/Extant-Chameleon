<?php

namespace App\Transformers;


class CpOrderItemToSeLineItemsTransformer
{
    public static function transform($items): array
    {
        $data = [];

        foreach($items as $item){

            $item = collect($item)->toArray();

            $data[] = [
                'ext_line_item_id' => $item['id'],
                'sku' => $item['manufacturer_sku'],
                'item_name' => $item['name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
            ];
        }

        return $data;
    }
}
