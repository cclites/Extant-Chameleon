<?php

namespace App\Transformers;


class CpAddressToSsAddressTransformer
{
    /**
     * @param array $cpAddress
     * @param string $customerName
     * @return array
     */
    public static function transform(array $cpAddress, string $customerName)
    {
        $cpAddress = $cpAddress[0];

        return [
            'name' => $customerName,
            'street1' => $cpAddress['line_1'],
            'street2' => !empty($cpAddress['line_2']) ? $cpAddress['line_2'] : null,
            'city' => $cpAddress['city'],
            'state' => $cpAddress['state'],
            'postalCode' => $cpAddress['zip'],
            'country' => 'US' //TODO: If users from other countries come on board, this will
                              //      need to be changed to a field.
        ];
    }
}
