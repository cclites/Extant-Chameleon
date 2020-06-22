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
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //GETTING ORDERS FROM CONTROLPAD
        if(env('APP_DEBUG') === true){
            $baseUrl = config('sscp.CP_DEV_BASE_PATH');
            $apiKey = config('sscp.CP_DEV_API_KEY');
        }else{
            $baseUrl = config('sscp.CP_BASE_PATH');
            $apiKey = config('sscp.CP_API_KEY');
        }

        $startDate = Carbon::yesterday()->subMonths(2)->startOfDay();
        $endDate = Carbon::today()->endOfDay();

        $controlPad = new ControlPadDataModel($baseUrl, $apiKey, $startDate, $endDate);
        $orders = $controlPad->get();

        if(!$orders){
            echo "\nNo Orders to process.";
            die();
        }else{
            echo "\nOrders are: \n";
            echo "\n\n" . json_encode($orders) . "\n";
        }

        $ids = collect($orders->data)->map(function ($order){
            return $order->id;
        })->toArray();

        // TODO::
        //  Unused for now. Will be used to verify orders once I know what
        //  happens with ShipStation if a single order fails out of multiple
        //  orders.
        $ordersMap = collect($orders->data)->map(function($order){
            return [ $order->receipt_id => [
               'id' => $order->id
            ]];
        });

        $shipStation = new ShipStationDataModel();
        $transformedOrders = $shipStation->formatOrders($orders->data);

        //TODO:: What happens when one fails out of a bunch of records?

        $shipStation->post($transformedOrders);

        //***********************  UPDATE CP Orders
        $controlPad->patch($ids);
    }
}
