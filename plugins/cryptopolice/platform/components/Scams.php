<?php namespace CryptoPolice\Platform\Components;

use Flash;
use Cms\Classes\ComponentBase;
use CryptoPolice\Platform\Models\Scam;

class Scams extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Scam list',
            'description' => 'Scam list'
        ];
    }

    public function onRun()
    {
        
    }

    public function onAddScam() {

        $scam = new Scam();
        $scam->save([
                'title' => post('scam_title'),
                'description' => post('scam_description')
            ]
        );

        Flash::success('test');
        return;
    }
}