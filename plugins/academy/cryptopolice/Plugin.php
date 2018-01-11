<?php namespace Academy\CryptoPolice;

use Event;
use ApplicationException;
use System\Classes\PluginBase;
use RainLab\User\Models\User as UserModel;
use RainLab\User\Controllers\Users as UsersController;



class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
            'Academy\Cryptopolice\Components\Exams' => 'Exams',
            'Academy\Cryptopolice\Components\ExamTask' => 'ExamTask',
            'Academy\Cryptopolice\Components\Trainings' => 'Trainings',
            'Academy\Cryptopolice\Components\ProfileForm' => 'ProfileForm',
            'Academy\Cryptopolice\Components\TrainingTask' => 'TrainingTask',
        ];
    }

    public function registerSettings()
    {
    }

    public function boot()
    {
        Event::listen('rainlab.user.beforeRegister', function($user) {

            $userPassword = post('password');

            if(!preg_match('/[a-zA-Z]/',$userPassword)) {
                throw new ApplicationException('Password should contain at least one letter character');
            }

            if(!preg_match('/[^a-zA-Z\d]/',$userPassword)) {
                throw new ApplicationException('Password should contain at least one special character');
            }

        });
    }
}
