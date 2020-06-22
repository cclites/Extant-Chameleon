<?php

namespace App\Console\Commands;

use App\ControlPad;
use App\Shipstation;
use App\DataModels\ControlPadDataModel;
use App\DataModels\ShipStationDataModel;

use Carbon\Carbon;
use Illuminate\Console\Command;

class ControlPanelToShipStation extends Command
{

    public $startDate;

    public $endDate;

    public $controlPad;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sscp:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Starts process that grabs unprocessed orders from ControlPad and inserts them into ShipStation';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {

        echo "\nCalled the Constructor\n";

        parent::__construct();

        $this->startDate = Carbon::yesterday()->subMonth()->startOfDay();
        $this->endDate = Carbon::now();
        $this->controlPad = new ControlPadDataModel($this->startDate, $this->endDate);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        echo "Handle the job\n";

        //Get unfulfilled orders from control pad
        $orders = $this->controlPad->get();

        if(!$orders->data){
            echo "There are no orders to update\n";
            \Log::error("There are no orders to update.");
            return false;
        }

        echo "Retrieved orders\n";

        $ids = collect($orders->data)->map(function ($order){
            return $order->id;
        })->toArray();

        if(!count($ids)){
            echo "Unable to pull id from data.\n";
            \Log::error("Unable to pull id from data.");
            return false;
        }

        echo "Id array is populated.\n";

        $shipStation = new ShipStationDataModel();
        $transformedOrders = $shipStation->formatOrders($orders->data);

        if(!$transformedOrders){
            echo "Unable to process transformed orders.\n";
            \Log::error("Unable to process transformed orders.");
            return false;
        }

        \Log::info("Have transformed orders");

        $response = $shipStation->post($transformedOrders);

        echo "\nSHIPSTATION RESPONSE: \n" . json_encode($response) . "\n\n";

        /*
        if($response-){
            \Log::error("Unable to process transformed orders.");
            return false;
        }*/

        //***********************  UPDATE CP Orders
        $this->controlPad->patch($ids);
    }
}
