<?php namespace CryptoPolice\CryptoPolice\Components;

use Flash;
use Cms\Classes\ComponentBase;
use CryptoPolice\CryptoPolice\Models\Training;

class TrainingTask extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'Training Task',
            'description' => 'Training Task for officer.'
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
    public $task;
    public function onAcceptTraining() {
        Flash::success('test');
    }
    public function onRun() {
        
        $slug = $this->param('slug');
        $task = Training::where('slug', $slug)->get();

        if(!$task) {
            return $this->controller->run('404'); 
        } else {
            $this->task = $task;
        }
    }

}
