<?php namespace CryptoPolice\Bounty\Components;

use Cms\Classes\ComponentBase;
use CryptoPolice\Bounty\Models\Report;

class FilterReports extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Report Filter',
            'description' => 'Report Filter bar'
        ];
    }

    public function onRun()
    {
        
    }

}
