<?php namespace CryptoPolice\Platform\Components;

use Flash;
use Cms\Classes\ComponentBase;
use CryptoPolice\Platform\Models\Scam;

class ScamDetails extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'          => 'Scam description',
            'description'   => 'Scam description'
        ];
    }

    public function onRun()
    {
        $this->page['scam'] = $this->getScam();
    }

    public function getScam()
    {
        return Scam::where('id', $this->param('id'))->first();
    }
}