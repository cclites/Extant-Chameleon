<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\ControlPad;
use App\User;
use Carbon\Carbon;
use App\Repositories\ControlPadRepository;
use App\Repositories\ShipStationRepository;

/**
 * Class ControlPanelToShipStation
 *
 * Cron job to pull unfulfilled orders from ControlPad,
 * inserts then orders into ShipStation, and then
 * updates the ControlPad order
 *
 * @package App\Console\Commands
 */
class ControlPanelToShipStation extends Command
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
     * @var array
     */
    public $authConfigs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:process-cp-to-ss';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grabs unfulfilled orders from ControlPad and inserts them into ShipStation';

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
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $clients = config('auths.CLIENTS.SHIPSTATION');

        foreach($clients as $client){

            $this->authConfigs = config('auths.SHIPSTATION.' . $client);
            $this->processOrders($client);
        }
    }

    public function processOrders($client)
    {

        $shipStation = new ShipStationRepository($this->authConfigs);
        $controlPad = new ControlPadRepository($this->authConfigs, $this->startDate, $this->endDate);

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
        // 3. Convert CP Order data to SS order data
        //**************************************************
        $transformedOrders = $shipStation->formatOrders($orders->data);

        if(!$transformedOrders){
            echo "Unable to process transformed orders.\n";
            \Log::error("Unable to process transformed orders.");
            return false;
        }

        //**************************************************
        // 4. Post orders to ShipStation
        //**************************************************
        $response = $shipStation->post($transformedOrders);

        if(!$response){
            echo "No response when posting orders to ShipStation.\n";
            \Log::error("No response when posting orders to ShipStation.");
            return false;
        }

        //**************************************************
        // 5. Update ControlPad orders tp status_pending
        //**************************************************
        $controlPad->patch($ids);

        return true;
    }
}
