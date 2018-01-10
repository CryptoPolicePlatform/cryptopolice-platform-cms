<?php namespace Academy\CryptoPolice\Components;

use Auth;
use Redirect;
use Response;
use Cms\Classes\ComponentBase;
use Academy\CryptoPolice\Models\Training;

class Trainings extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'Training List',
            'description' => 'Training List of tasks.'
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

    public $trainings;

    public function onRun() {

        $loggedIn = Auth::check();
        if(!$loggedIn) {
            return Redirect::to('/login');
        }

        $training = Training::paginate(10);
       
        if(!$training) {
           return $this->controller->run('404');
        } else {
            $this->trainings = $training;
        }
    }

}
