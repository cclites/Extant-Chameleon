<?php


namespace App\DataModelControllers;


use App\ControlPad;

class BaseDataModelController
{
    protected $CpBasePath;
    protected $CpApiKey;
    protected $SsBasePath;

    public function boot()
    {
        if(env('APP_DEBUG') === true)
        {
            $this->CpBasePath = config('sscp.CP_DEV_BASE_PATH');
        }else{
            $this->CpBasePath = config('sscp.CP_BASE_PATH');
        }

        $this->SsBasePath = config('sscp.SS_BASE_PATH');
    }
}
