<?php

namespace App\Console\Commands;

use App\Http\Controllers\ShipStationController;
use App\Http\Resources\ControlPadResource;
use Illuminate\Console\Command;
use Carbon\Carbon;
use GuzzleHttp\Client;

use App\DataModels\ControlPadDataModel;
use App\DataModels\ShipStationDataModel;

use CpOrderFactory;

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

        $path = "https://ssapi11.shipstation.com/shipments?batchId=17908484&includeShipmentItems=False";

        //Test Endpoint
        //$path = 'https://extant.digital/sscp/public/';
        //$order = CpOrderFactory::create();



        $response = $this->client->request(
            'GET',
            $path,
            [
                //'debug' => env('APP_DEBUG'),
                'headers' => $this->headers
            ]
        );


        echo "\n\n" . $response->getBody()->getContents() . "\n";

        //$this->controlPad->addTracking(json_encode($response->getBody()->getContents()));

    }
}
