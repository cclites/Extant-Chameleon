<?php


namespace App\DataModels;


use App\ControlPad;

class BaseDataModel
{
    protected $CpBasePath;
    protected $CpApiKey;
    protected $SsBasePath;

    public function boot()
    {
        if(env('APP_DEBUG') === true)
        {
            $this->CpApiKey = config('sscp.CP_DEV_API_KEY');
            $this->CpBasePath = config('sscp.CP_DEV_BASE_PATH');
        }else{

            $this->CpApiKey = config('sscp.CP_API_KEY');
            $this->CpBasePath = null;
        }

        $this->SsBasePath = config('sscp.SS_BASE_PATH');
    }
}
