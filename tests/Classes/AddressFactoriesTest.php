<?php

use App\Repositories\ControlPadRepository;
use App\Repositories\ShipStationRepository;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;

use GuzzleHttp\Client;
use Faker\Factory as Faker;


class AddressFactoriesTest extends TestCase
{
    public $controlPadAddress;
    public $shipStationAddress;
    public $shippingEasyAddress;

    public function setUp() : void
    {
        parent::Setup();
    }

    public function test_ss_address_has_name(): void
    {
        $address = \App\Libraries\factories\AddressFactory::ssCreate();
        $this->assertArrayHasKey('name', $address);
    }

    public function test_ss_address_has_street1(): void
    {
        $address = \App\Libraries\factories\AddressFactory::ssCreate();
        $this->assertArrayHasKey('street1', $address);
    }

    public function test_ss_address_has_street2(): void
    {
        $address = \App\Libraries\factories\AddressFactory::ssCreate();
        $this->assertArrayHasKey('street2', $address);
    }

    public function test_ss_address_has_city(): void
    {
        $address = \App\Libraries\factories\AddressFactory::ssCreate();
        $this->assertArrayHasKey('city', $address);
    }

    public function test_ss_address_has_state(): void
    {
        $address = \App\Libraries\factories\AddressFactory::ssCreate();
        $this->assertArrayHasKey('state', $address);
    }

    public function test_ss_address_has_postal_code(): void
    {
        $address = \App\Libraries\factories\AddressFactory::ssCreate();
        $this->assertArrayHasKey('postalCode', $address);
    }

    public function test_ss_address_has_country(): void
    {
        $address = \App\Libraries\factories\AddressFactory::ssCreate();
        $this->assertArrayHasKey('country', $address);
    }


    public function tearDown(): void
    {
    }
}

/**
 * 'name' => $customerName,
'street1' => $cpAddress['line_1'],
'street2' => !empty($cpAddress['line_2']) ? $cpAddress['line_2'] : null,
'city' => $cpAddress['city'],
'state' => $cpAddress['state'],
'postalCode' => $cpAddress['zip'],
'country' => 'US'
 */
