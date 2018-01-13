<?php namespace CryptoPolice\NewAcademy\Components;

use Auth;
use Redirect;
use Response;
use Cms\Classes\ComponentBase;
use CryptoPolice\newAcademy\Models\Training;

class TrainingsUnconfirmed extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'Training List (Unconfirmed)',
            'description' => 'Training List of (Unconfirmed) tasks.'
        ];
    }

    public function defineProperties()
    {
        return [
            'max' => [
                'description'       => 'The most amount of todo items allowed',
                'title'             => 'Max items',
                'default'           => 10,
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'The Max Items value is required and should be integer.'
            ]
        ];
    }

    public $trainingsUnc;

    public function onRun() {

        $loggedIn = Auth::check();
        if(!$loggedIn) {
            return Redirect::to('/login');
        }

        $training = Training::where('status','=','0')->orderBy('likes','desc')->paginate(10);
       
        if(!$training) {
           return $this->controller->run('404');
        } else {
            $this->trainingsUnc = $training;
        }
    }

}
