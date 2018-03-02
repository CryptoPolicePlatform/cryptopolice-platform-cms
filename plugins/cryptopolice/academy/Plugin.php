<?php namespace CryptoPolice\Academy;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{

    public $require = [
        'RainLab.Location',
        'RainLab.Notify',
        'RainLab.User',
    ];

    public function registerComponents()
    {
        return [
            'CryptoPolice\Academy\Components\Exams'          => 'Exams',
            'CryptoPolice\Academy\Components\ExamTask'       => 'ExamTask',
            'CryptoPolice\Academy\Components\Recaptcha'      => 'reCaptcha',
            'CryptoPolice\Academy\Components\Trainings'      => 'Trainings',
            'CryptoPolice\Academy\Components\TrainingTask'   => 'TrainingTask',
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'         => 'Platform Settings',
                'description'   => 'Settings',
                'icon'          => 'icon-cog',
                'class'         => 'CryptoPolice\Academy\Models\Settings',
            ]
        ];
    }

    public function boot()
    {

    }

}