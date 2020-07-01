<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\ControlPad;
use App\User;
use Carbon\Carbon;
use App\DataModelControllers\ControlPadModelController;
use App\DataModelControllers\ShipStationModelController;

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
     * @var ControlPadModelController
     */
    public $controlPad;

    /**
     * @var ShipStationModelController
     */
    public $shipStation;

    /**
     * @var array
     */
    public $auths;

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

        $this->startDate = config('sscp.SSCP_START_DATE');
        $this->endDate = config('sscp.SSCP_END_DATE');

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $clients = config('auths.CLIENTS');

        foreach($clients as $client){

            $this->auths = config('auths.' . $client);
            $shipStation = new ShipStationModelController($this->auths);
            $controlPad = new ControlPadModelController($this->auths, $this->startDate, $this->endDate);

            $orders = $controlPad->get('unfulfilled');

            $this->processOrders();

        }

        //Process clients one by one.
        $this->processOrders();

    }

    public function processOrders()
    {
        $controlPad = new ControlPadModelController($this->auths, $this->startDate, $this->endDate);

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
        $ids = collect($orders->data)->pluck('id');

        if(!count($ids)){
            echo "Unable to pull id from data.\n";
            \Log::error("Unable to pull id from data.");
            return false;
        }

        echo "Id array is populated.\n";

        //**************************************************
        // 3. Convert CP Order data to SS order data
        //**************************************************
        $transformedOrders = $this->shipStation->formatOrders($orders->data);

        if(!$transformedOrders){
            echo "Unable to process transformed orders.\n";
            \Log::error("Unable to process transformed orders.");
            return false;
        }

        //**************************************************
        // 4. Post orders to ShipStation
        //**************************************************
        $response = $this->shipStation->post($transformedOrders);

        if(!$response){
            echo "No response when posting orders to ShipStation.\n";
            \Log::error("No response when posting orders to ShipStation.");
            return false;
        }

        //**************************************************
        // 5. Update ControlPad orders tp status_pending
        //**************************************************
        $this->controlPad->patch($ids);

        return true;
    }
}
