<?php

use Tests\TestCase;

use App\Http\Resources\ControlPadResource;
use App\Libraries\factories\CpOrderFactory;

class ControlPadTransformersTest extends TestCase
{
    public $order;

    public function setUp(): void
    {
        parent::Setup();
        $this->order = CpOrderFactory::create();
    }

    public function test_cp_order_factory(): void
    {
        $this->assertNotNull($this->order);
    }

    /*********************************************************************
     * ControlPad to ShipStation transforms
     *********************************************************************/

    public function test_cp_order_item_has_receipt_id(): void
    {
        $this->assertArrayHasKey('receipt_id', $this->order);
    }

    public function test_cp_order_item_has_total_price(): void
    {
        $this->assertArrayHasKey('total_price', $this->order);
    }

    public function test_cp_order_item_has_total_tax(): void
    {
        $this->assertArrayHasKey('total_tax', $this->order);
    }

    public function test_cp_order_item_has_billing_address(): void
    {
        $this->assertArrayHasKey('billing_address', $this->order);
    }

    public function test_cp_order_item_has_shipping_address(): void
    {
        $this->assertArrayHasKey('shipping_address', $this->order);
    }

    public function test_cp_order_item_has_lines(): void
    {
        $this->assertArrayHasKey('lines', $this->order);
    }

    public function test_can_convert_cp_order_item_to_ss_order_item(): void
    {
        $transformedItem = ControlPadResource::transformCPOrderItemToSSOrderItem( $this->order['lines'][0] );
        $this->assertNotNull($transformedItem);
    }

    /*
    public function test_can_convert_cp_address_to_ss_address(): void
    {
        $transformedAddress = ControlPadResource::transformCPAddressToSSAddress($this->order['shipping_address'][0], "James Dough");
        $this->assertNotNull($transformedAddress);
    }*/

    public function test_convert_control_pad_to_ss_order(): void
    {
        $transformedOrder = ControlPadResource::transformCPOrderToSSOrder($this->order);
        $this->assertNotNull($transformedOrder);
    }

    /*********************************************************************
     * ControlPad to ShippingEasy transforms
     *********************************************************************/
    public function test_can_convert_cp_order_item_to_se_order_item(): void
    {
        $transformedItem = ControlPadResource::transformCPOrderItemToSEOrderItem($this->order['lines']);
        $this->assertNotNull($transformedItem);
    }

    public function test_can_convert_cp_buyer_to_se_recipients(): void
    {
        $transformedRecipient = ControlPadResource::transformCPRecipientToSERecipient($this->order);
        $this->assertNotNull($transformedRecipient);
    }

    public function test_can_convert_cp_order_to_se_order(): void
    {
        $transformedOrder = ControlPadResource::transformCPOrderToSEOrder($this->order);
        $this->assertNotNull($transformedOrder);
    }

}
