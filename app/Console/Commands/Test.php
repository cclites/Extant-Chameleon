<?php

namespace App\Console\Commands;

use App\ControlPad;
use App\Http\Resources\ControlPadResource;
use App\Libraries\factories\ShippingEasyOrderFactory;
use App\Repositories\ShippingEasyRepository;
use Illuminate\Console\Command;
use Carbon\Carbon;
use GuzzleHttp\Client;

use App\Repositories\ControlPadRepository;
use App\Repositories\ShipStationRepository;
use App\Libraries\ControlPadTrackingFactory;


/**
 * Class ControlPanelToShipStation
 *
 * Cron job to pull unfulfilled orders from ControlPad,
 * inserts then orders into ShipStation, and then
 * updates the ControlPad order
 *
 * @package App\Console\Commands
 */
class Test extends Command
{
    /**
     * @var Carbon
     */
    public $startDate;

    /**
     * @var Carbon
     */
    public $endDate;

    /**
     * @var ControlPadRepository
     */
    public $controlPad;

    /**
     * @var ShipStationRepository
     */
    public $shipStation;

    /**
     * @var array
     */
    public $headers;

    /**
     * @var array
     */
    public $authConfigs;

    /**
     * @var GuzzleClient
     */
    public $client;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:stuff';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->startDate = config('sscp.CP_ORDERS_START');
        $this->endDate = config('sscp.CP_ORDERS_END');
        $this->authConfigs = config('auths.SHIPPINGEASY.DEV_1');

        require_once "app/Libraries/integration_wrappers/ShippingEasy/lib/ShippingEasy.php";
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //see if I have auths
        if(!$this->authConfigs){
            die("No configs");
        }

        $authConfig = $this->authConfigs;
        $shippingEasy = new ShippingEasyRepository($authConfig);
        $controlPad = new ControlPadRepository($authConfig, $this->startDate, $this->endDate);

        $orders = $controlPad
            ->get(ControlPad::DEFAULT_STATUS);

        /*
        foreach($orders->data as $order){

            if(!property_exists($order, 'lines')){
                echo "I HAVE NO LINES\n";
            }else{
                foreach ($order->lines as $item){

                    //die(json_encode($item));

                    if(!filled($item->items)){
                        echo "I HAVE NO ITEMS in {$order->id}\n";
                    }
                }
            }

        }*/

        $transformedOrders = $shippingEasy->formatOrders($orders->data);
        //dd($transformedOrders);

        //foreach ($transformedOrders as $order){

            //echo json_encode($order) . "\n";
            //die();

        //}

        //$response = $shippingEasy->post($transformedOrders);
        //dd($response);

        /*
        collect($orders->data)->each(function ($order){

            echo $order->id . "\n";
            //echo json_encode($order) . "\n";
            //echo gettype($order) . "\n";
            return;

        });*/

    }
}
