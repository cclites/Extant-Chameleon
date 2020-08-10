<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\ControlPad;
use App\User;
use Carbon\Carbon;
use App\Repositories\ControlPadRepository;
use App\Repositories\ShippingEasyRepository;


/**
 * Class ControlPanelToShipStation
 *
 * Cron job to pull unfulfilled orders from ControlPad,
 * inserts then orders into ShipStation, and then
 * updates the ControlPad order
 *
 * @package App\Console\Commands
 */
class ControlPanelToShippingEasy extends Command
{
    /**
     * @var Carbon
     */
    public $startDate;

    /**
     * @var Carbon
     */
    public $endDate;

    public $authConfigs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:process-cp-to-se';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grabs unfulfilled orders from ControlPad and inserts them into ShippingEasy';

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

        require_once "app/Libraries/integration_wrappers/ShippingEasy/lib/ShippingEasy.php";


    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $clients = config('auths.CLIENTS.SHIPPINGEASY');

        foreach($clients as $client){

            $this->authConfigs = config('auths.SHIPPINGEASY.' . $client);

            \ShippingEasy::setApiKey($this->authConfigs['ApiKey']);
            \ShippingEasy::setApiSecret($this->authConfigs['ApiSecret']);

            $this->processOrders($client);
        }

    }

    public function processOrders($client)
    {
        $authConfig = $this->authConfigs;
        $shippingEasy = new ShippingEasyRepository($authConfig);
        $controlPad = new ControlPadRepository($authConfig, $this->startDate, $this->endDate);

        //**************************************************
        // 1. Get unfulfilled orders from ControlPad
        //**************************************************
        $orders = $controlPad
                    ->get(ControlPad::DEFAULT_STATUS);



        if(!$orders->data){
            echo "There are no orders to update\n";
            \Log::info("There are no orders to update.");
            return false;
        }

        echo "Retrieved orders\n";

        //**************************************************
        // 2. Build an array of CP order ids
        //**************************************************
        $ids = collect($orders->data)->pluck('id')->toArray();

        if(!count($ids)){
            echo "Unable to pull id from data.\n";
            \Log::error("Unable to pull id from data.");
            return false;
        }

        echo "Id array is populated.\n";

        //**************************************************
        // 3. Convert CP Order data to SE order data
        //**************************************************
        $transformedOrders = $shippingEasy->formatOrders($orders->data);

        if(!$transformedOrders){
            echo "Unable to process transformed orders.\n";
            \Log::error("Unable to process transformed orders.");
            return false;
        }

        echo "Orders have been transformed.\n";

        //**************************************************
        // 4. Post orders to ShippingEasy
        //**************************************************
        $response = $shippingEasy->post($transformedOrders);


        if(!$response){
            echo "No response when posting orders to ShippingEasy.\n";
            \Log::error("No response when posting orders to ShippingEasy.");
            return false;
        }

        //**************************************************
        // 5. Update ControlPad orders tp status_pending
        //**************************************************
        //$controlPad->patch($ids);

        return true;
    }
}
