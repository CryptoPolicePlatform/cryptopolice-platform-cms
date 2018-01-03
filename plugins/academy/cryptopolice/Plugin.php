<?php namespace Academy\CryptoPolice;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    { 
	return [
            'Cryptopolice\NewAcademy\Components\Exams' => 'Exams',
            'Cryptopolice\NewAcademy\Components\ExamTask' => 'ExamTask'
        ];
    }

    public function registerSettings()
    {
    }
}
