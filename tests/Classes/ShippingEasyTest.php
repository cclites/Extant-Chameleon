<?php

use Tests\TestCase;
use GuzzleHttp\Client;
use App\Libraries\factories\ShippingEasyOrderFactory;
use App\Repositories\ShippingEasyRepository;
use Carbon\Carbon;


class ShippingEasyTest extends TestCase
{
    public $startDate;
    public $endDate;
    public $shippingEasy;
    public $client;

    public function setUp() : void
    {
        parent::Setup();

        //require_once "app/Libraries/integration_wrappers/ShippingEasy/lib/ShippingEasy.php";

        $auths = config('auths.SHIPPINGEASY.DEV_1');

        $this->startDate = config('sscp.CP_ORDERS_START');
        $this->endDate = config('sscp.CP_ORDERS_END');

        $this->shippingEasy = ShippingEasyOrderFactory::create()[0];



    }

    public function test_can_create_shipping_easy(): void
    {
        $this->assertNotNull($this->shippingEasy);
    }

    public function test_has_external_order_identifier(): void
    {
        $this->assertArrayHasKey('external_order_identifier', $this->shippingEasy);
    }

    public function test_has_alternate_order_id(): void
    {
        $this->assertArrayHasKey('alternate_order_id', $this->shippingEasy);
    }

    public function test_has_ordered_at(): void
    {
        $this->assertArrayHasKey('ordered_at', $this->shippingEasy);
    }

    public function test_has_order_status(): void
    {
        $this->assertArrayHasKey('order_status', $this->shippingEasy);
    }

    public function test_has_total_including_tax(): void
    {
        $this->assertArrayHasKey('total_including_tax', $this->shippingEasy);
    }

    public function test_has_total_tax(): void
    {
        $this->assertArrayHasKey('total_tax', $this->shippingEasy);
    }

    public function test_has_shipping_cost_including_tax(): void
    {
        $this->assertArrayHasKey('shipping_cost_including_tax', $this->shippingEasy);
    }

    public function test_has_billing_first_name(): void
    {
        $this->assertArrayHasKey('billing_first_name', $this->shippingEasy);
    }

    public function test_has_billing_last_name(): void
    {
        $this->assertArrayHasKey('billing_first_name', $this->shippingEasy);
    }

    public function test_has_billing_address(): void
    {
        $this->assertArrayHasKey('billing_address', $this->shippingEasy);
    }

    public function test_has_billing_address_2(): void
    {
        $this->assertArrayHasKey('billing_address_2', $this->shippingEasy);
    }

    public function test_has_billing_city(): void
    {
        $this->assertArrayHasKey('billing_city', $this->shippingEasy);
    }

    public function test_has_billing_state(): void
    {
        $this->assertArrayHasKey('billing_state', $this->shippingEasy);
    }

    public function test_has_billing_postal_code(): void
    {
        $this->assertArrayHasKey('billing_postal_code', $this->shippingEasy);
    }

    public function test_has_billing_phone_number(): void
    {
        $this->assertArrayHasKey('billing_phone_number', $this->shippingEasy);
    }

    public function test_has_billing_country(): void
    {
        $this->assertArrayHasKey('billing_country', $this->shippingEasy);
    }

    public function test_has_recipients(): void
    {
        $this->assertArrayHasKey('recipients', $this->shippingEasy);
    }

    public function test_has_at_least_one_recipient(): void
    {
        $this->assertCount(1,  $this->shippingEasy['recipients']);

    }

    public function test_has_at_least_one_order_line(): void
    {
        $this->assertCount(1,  $this->shippingEasy['recipients'][0]['line_items']);

    }


}
