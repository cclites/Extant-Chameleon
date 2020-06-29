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
class Setup extends Command
{
    public $shipStation;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sscp:setup';

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
        $this->shipStation = new ShipStationDataModel();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //Deprecated due to change in architecture - architecture change

        //id = 4960

        /*
        $delete = false;
        $response = $this->shipStation->addOrderShippedWebHook();

        $contents = \GuzzleHttp\json_decode($response->getBody()->getContents());

        if($delete){
            $id = $contents->id;
            $this->shipStation->removeSsWebHook($contents->id);
        }

        echo "\n" . json_encode($contents) . "\n";
        */
    }
}
