<?php

use Tests\TestCase;
use GuzzleHttp\Client;
use App\Libraries\factories\ShippingEasyOrderFactory;
use App\Libraries\factories\CpOrderFactory;
use App\Repositories\ShippingEasyRepository;
use Carbon\Carbon;


class ShippingEasyFunctionsTest extends TestCase
{
    public $startDate;
    public $endDate;
    public $shippingEasy;
    public $client;
    public $cpOrder;
    public $auths;

    public function setUp() : void
    {
        parent::Setup();

        require_once "app/Libraries/integration_wrappers/ShippingEasy/lib/ShippingEasy.php";

        $this->auths = config('auths.SHIPPINGEASY.DEV_1');

        $this->startDate = config('sscp.CP_ORDERS_START');
        $this->endDate = config('sscp.CP_ORDERS_END');

        $this->shippingEasy = ShippingEasyOrderFactory::create();

        ShippingEasy::setApiKey($this->auths['ApiKey']);
        ShippingEasy::setApiSecret($this->auths['ApiSecret']);

        //Create a control pad order
        $this->cpOrder = CpOrderFactory::create();



        //convert to a shipping easy controller

    }

    public function test_can_create_cp_order(): void
    {
        $this->assertNotNull($this->cpOrder);
    }

    public function test_can_convert_cp_order_to_se_order(): void
    {
        $transformedOrders = \App\Http\Resources\ControlPadResource::transformCPOrderToSEOrder($this->cpOrder);
        $this->assertNotNull($transformedOrders);
    }

}
