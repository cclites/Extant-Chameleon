<?php

namespace App\Console\Commands;

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
class AddTrackingTest extends Command
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
    public $auths;

    /**
     * @var GuzzleClient
     */
    public $client;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:tracking';

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
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //Test PID
        $pid = '1uhxqtbcv5utau6fmf8a4eipp';
        //Test receipt_id
        $receiptId = 'OY6TKN-24';

        $authConfigs = config('auths.DEV_1');

        $this->startDate = Carbon::yesterday()->subMonths(4)->startOfDay();
        $this->endDate = Carbon::now();
        $this->controlPad = new ControlPadRepository($authConfigs, $this->startDate, $this->endDate);
        $this->shipStation = new ShipStationRepository($authConfigs);
        $this->headers = $this->shipStation->headers;

        $this->client = new Client();


        $results = $this->controlPad->get();

        //Retrieve the one record I want
        $order = collect($results->data)->where('pid', $pid)->first();

        if(!$order->receipt_id === $receiptId){
            echo "\nUnable to find correct record using PID, and validated by cross-checking receipt id\n";
            die();
        }

        echo "\nOrder status " . $order->status . "\n";

        //Now add tracking info
        $trackingData = ControlPadTrackingFactory::create($order);

        //Find the result I need
        //echo "\n" . json_encode($trackingData) . "\n";

        //Post tracking data to Control Pad
        $result = $this->controlPad->addTracking([$trackingData]);

        echo "\n" . json_encode($result) . "\n";



    }
}
