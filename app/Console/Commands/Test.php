<?php

namespace App\Console\Commands;

use App\Http\Resources\ControlPadResource;
use Illuminate\Console\Command;
use Carbon\Carbon;
use GuzzleHttp\Client;

use App\DataModels\ControlPadDataModel;
use App\DataModels\ShipStationDataModel;

use App\ControlPad;
use App\ShipStation;

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
     * @var ControlPadDataModel
     */
    public $controlPad;

    /**
     * @var ShipStationDataModel
     */
    public $shipStation;

    /**
     * @var
     */
    public $headers;

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

        $this->startDate = Carbon::yesterday()->subMonths(4)->startOfDay();
        $this->endDate = Carbon::now();
        $this->controlPad = new ControlPadDataModel($this->startDate, $this->endDate);
        $this->shipStation = new ShipStationDataModel();
        $this->headers = $this->shipStation->headers;
        $this->client = new Client();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $orders = $this->controlPad->get('pending')->data;

        echo "\n\n";
        //echo json_encode($order->lines) . "\n";

        collect($orders)->each(function($order){

            collect($order->lines)->each(function($line) use ($order){
                echo ControlPadResource::transformCPOrderItemToSSOrderItem($order, $line) . "\n\n";
            });
        });







        //echo json_encode($order) . "\n";

        //echo json_encode($result->getBody()->getContents()) . "\n";
    }
}
