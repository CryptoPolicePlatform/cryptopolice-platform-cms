<?php namespace CryptoPolice\Academy\Components;

use Auth, Redirect, Response;
use Cms\Classes\ComponentBase;
use CryptoPolice\Academy\Models\Training;

class TrainingsUnconfirmed extends ComponentBase
{

    public $trainingsUnc;

    public function componentDetails()
    {
        return [
            'name'        => 'Training List (Unconfirmed)',
            'description' => 'Training List of (Unconfirmed) tasks.'
        ];
    }

    public function onRun()
    {
        $training = Training::where('status','=','0')
            ->orderBy('likes','desc')
            ->paginate(10);
       
        if($training) {
           return $this->controller->run('404');
        } else {
            $this->trainingsUnc = $training;
        }
    }

}
