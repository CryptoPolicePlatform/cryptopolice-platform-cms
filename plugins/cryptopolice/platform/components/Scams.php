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
        $this->page['scams'] = Scam::paginate(15);
    }

    public function onAddScam() {

    }
}