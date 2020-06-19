<?php

require 'vendor/autoload.php';

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use GuzzleHttp\Client;
use Faker\Factory as Faker;

use App\Libraries\AddressFactory;

class ApiTest extends TestCase
{

    public $endpoint = 'https://ssapi.shipstation.com/';

    public $header;

    public $order;

    public function setUp(): void
    {
        parent::setUp();


        $rawToken = $this->generateRawAuthenticationToken();
        $encryptedToken = $this->encryptRawAuthenticationToken($rawToken);

        $this->header = [
            'Authorization' => 'Basic ' . $encryptedToken,
            'Content-Type' => 'application/json'
        ];

        $this->order = $this->createOrder();
    }

    /*
    public function testCredentialsExist()
    {
        echo "\ntestCredentialsExist\n";

        $this->assertNotEmpty(env('SHIPSTATION_API_KEY'));
        $this->assertNotEmpty(env('SHIPSTATION_API_SECRET'));
    }

    public function testGuzzleInstalled()
    {
        echo "\ntestGuzzleInstall\n";
        $this->assertIsObject(new Client());
    }

    public function testCanConnectToShipStation()
    {
        echo "\ntestConnect\n";

        $client = new Client([
            'base_uri' => $this->endpoint,
            'headers' => $this->headers
        ]);

        $response = $client->get('orders', [
            'debug' => TRUE,
            'form_params' => [],
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ]
        ]);

        $this->assertEquals('200', $response->getStatusCode() );
    }


    public function testCanAddOrder(){

        $client = new Client([
            'base_uri' => $this->endpoint,
            'headers' => $this->header,
            'debug' => TRUE,
            'form_params' => $this->order
        ]);

        $response = $client->post('orders/createorder');

        \Log::info(json_encode($response->getBody()));

        $this->assertEquals('200', $response->getStatusCode() );
    }
    */

    /*
    public function testCanRetrieveOrder(){

        echo "\ntestCanRetrieveOrder\n";

        $client = new Client([
            'base_uri' => $this->endpoint,
            'headers' => $this->header,
            //'debug' => TRUE,
            'form_params' => $this->order
        ]);

        $response = $client->post('orders/createorder');

        $this->assertEquals('200', $response->getStatusCode() );

        $postedOrder = json_decode($response->getBody()->getContents());

        $orderId = $postedOrder->orderId;

        $client = new Client([
            //'base_uri' => '/orders/orderId=' . $orderId,
            'headers' => $this->header,
            //'debug' => TRUE,
            'form_params' => []
        ]);

        $response = $client->request('GET', $this->endpoint . '/orders/' . $orderId, []);

        $retrievedOrder = json_decode($response->getBody()->getContents());

        $this->assertEquals('200', $response->getStatusCode() );

        if($retrievedOrder->orderId === $orderId){
            $this->assertTrue(true);
        }else{
            $this->assertTrue(false);
        }

    }*/

    public function testCanConnectToControlPad(){

        /*
        $baseDevUrl = 'https://orders-api.controlpad.dev';

        $devApiKey = 'YXhvYzQ2RTJ4Vjk3M2xjMmdwc08wbVJlbmJJa21nMDJsMTFjOGc0YmhEbDY5NWE0';

        $startDate = \Carbon\Carbon::yesterday()->subMonth()->startOfDay();
        $endDate = \Carbon\Carbon::yesterday()->endOfDay();

        $fullUrl = $baseDevUrl . '/orders?per_page=3&start_date=' . $startDate .'&end_date=' . $endDate;

        $client = new Client();

        $response = $client->request(
            'GET',
            $fullUrl,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'APIKey' => $devApiKey
                ]
            ]
        );

        echo "\n\nSTATUS CODE: " . $response->getStatusCode() . "\n\n";

        \Log::info($response->getBody()->getContents());
        */
    }

    public function testCanCreateShipNotifyWebhook(){

        $targetUrl = 'http://192.168.10.10/orders/ship-notify';

        $body = [
            'target_url' => $targetUrl,
            'event' => 'SHIP_NOTIFY',
            'friendly_name' => ''
        ];

        $client = new Client([
            'base_uri' => $this->endpoint,
            'headers' => $this->header,
            'debug' => TRUE,
            'form_params' => $body
        ]);

        $response = $client->post('/webhooks/subscribe');

        $data = json_decode($response->getBody()->getContents());

        $this->assertObjectHasAttribute($data->id);
    }

    /****************************************************
     * UTILITIES
     ****************************************************/

    public function generateRawAuthenticationToken()
    {
        return env('SHIPSTATION_API_KEY') . ":" . env('SHIPSTATION_API_SECRET');
    }

    public function encryptRawAuthenticationToken(string $rawToken)
    {
        //return quoted_printable_encode ( $rawToken );
        return base64_encode($rawToken);
    }

    public function generateAuthorizationHeader(string $encryptedToken)
    {
        return 'Authorization: Basic ' . $encryptedToken;
    }


    public function addOrder(){

    }

    public function createOrder()
    {
        $faker = Faker::create();

        return [
            'orderNumber' => $faker->password(12),
            'orderDate' => \Carbon\Carbon::now()->format('m/d/Y'),
            'orderStatus' => $faker->randomElement(['awaiting_payment', 'awaiting_shipment', 'shipped', 'on_hold', 'cancelled']),
            'billTo' => AddressFactory::createAddress(),
            'shipTo' => AddressFactory::createAddress(),
        ];

    }


}
