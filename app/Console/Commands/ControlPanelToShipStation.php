<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ControlPanelToShipStation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ccsp:start';

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

    }
}
