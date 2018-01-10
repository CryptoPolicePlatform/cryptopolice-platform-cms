<?php namespace Academy\CryptoPolice;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
	return [
	    'Academy\Cryptopolice\Components\Exams' => 'Exams',
            'Academy\Cryptopolice\Components\ExamTask' => 'ExamTask',
            'Academy\Cryptopolice\Components\Trainings' => 'Trainings',
            'Academy\Cryptopolice\Components\TrainingTask' => 'TrainingTask'
	];
    }

    public function registerSettings()
    {
    }
}
