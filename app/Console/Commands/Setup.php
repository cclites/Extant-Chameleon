<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Repositories\ShipStationRepository;


/**
 * Setup
 *
 * 1. Create the ShipStation order_sent webhook
 *
 * @package App\Console\Commands
 */
class Setup extends Command
{
    /**
     * @var ShipStationRepository
     */
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
    protected $description = 'Sscp set-up';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->shipStation = new ShipStationRepository(config('auths.DEV_1'));
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->shipStation->createOrderShippedWebHook();
    }

}
