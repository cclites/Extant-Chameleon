<?php

namespace App\Console\Commands;

use App\Http\Controllers\ShipStationController;
use App\Http\Resources\ControlPadResource;
use App\User;
use Illuminate\Console\Command;
use Carbon\Carbon;
use GuzzleHttp\Client;

use App\DataModelControllers\ControlPadModelController;
use App\DataModelControllers\ShipStationModelController;

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
    public $headers;

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

        $credentials = User::transformSellerAuths(env('USER_DEV'));

        $this->startDate = Carbon::yesterday()->subMonths(4)->startOfDay();
        $this->endDate = Carbon::now();
        $this->controlPad = new ControlPadModelController($credentials, $this->startDate, $this->endDate);
        $this->shipStation = new ShipStationModelController();
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

    }
}
