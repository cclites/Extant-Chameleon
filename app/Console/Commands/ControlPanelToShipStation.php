<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

use App\DataModels\ControlPadDataModel;
use App\DataModels\ShipStationDataModel;

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
     * @var ControlPadDataModel
     */
    public $controlPad;

    /**
     * @var ShipStationDataModel
     */
    public $shipStation;

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
    protected $description = 'Starts process that grabs unfulfilled orders from ControlPad and inserts them into ShipStation';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->startDate = Carbon::yesterday()->subMonth()->startOfDay();
        $this->endDate = Carbon::now();
        $this->controlPad = new ControlPadDataModel($this->startDate, $this->endDate);
        $this->shipStation = new ShipStationDataModel();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //**************************************************
        // 1. Get unfulfilled orders from ControlPad
        //**************************************************
        $orders = $this->controlPad->get();

        if(!$orders->data){
            echo "There are no orders to update\n";
            \Log::error("There are no orders to update.");
            return false;
        }

        echo "Retrieved orders\n";

        //**************************************************
        // 2. Build an array of CP order ids
        //**************************************************
        $ids = collect($orders->data)->map(function ($order){
            return $order->id;
        })->toArray();

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
        // 5. Update ControlPad orders
        //**************************************************
        $this->controlPad->patch($ids);

        return true;
    }
}
