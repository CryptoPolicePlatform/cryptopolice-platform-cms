<?php namespace CryptoPolice\CryptoPolice;

use Auth;
use Event;
use ApplicationException;
use System\Classes\PluginBase;
use RainLab\User\Models\User as UserModel;
use RainLab\User\Controllers\Users as UsersController;
use RainLab\Notify\NotifyRules\SaveDatabaseAction;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
            'CryptoPolice\Cryptopolice\Components\Exams' => 'Exams',
            'CryptoPolice\Cryptopolice\Components\ExamTask' => 'ExamTask',
            'CryptoPolice\Cryptopolice\Components\Trainings' => 'Trainings',
            'CryptoPolice\Cryptopolice\Components\ProfileForm' => 'ProfileForm',
            'CryptoPolice\Cryptopolice\Components\TrainingTask' => 'TrainingTask',
            'CryptoPolice\Cryptopolice\Components\customUploader' => 'customUploader',
        ];
    }

    public function registerSettings()
    {
    }

    public function boot()
    {

        $this->extendUserModel();
        $this->extendUsersController();
        $this->extendSaveDatabaseAction();
        $this->extendUserModel();

        Event::listen('rainlab.user.beforeRegister', function ($user) {

            $userPassword = post('password');

            if (!preg_match('/[a-zA-Z]/', $userPassword)) {
                throw new ApplicationException('Password should contain at least one letter character');
            }

            if (!preg_match('/[^a-zA-Z\d]/', $userPassword)) {
                throw new ApplicationException('Password should contain at least one special character');
            }

        });
    }

    protected function extendUserModel()
    {
        UserModel::extend(function ($model) {
            $model->addFillable([
                'eth_address'
            ]);
        });
    }

    protected function extendUsersController()
    {
        UsersController::extendFormFields(function ($widget) {

            // Prevent extending of related form instead of the intended User form
            if (!$widget->model instanceof UserModel) {
                return;
            }

            $widget->addTabFields([
                'eth_address' => [
                    'label' => 'Ethereum wallet address',
                    'span' => 'left',
                    'tab' => 'Personal data'
                ],
            ]);

            // $configFile = plugins_path('rainlab/userplus/config/profile_fields.yaml');
            // $config = Yaml::parse(File::get($configFile));
            // $widget->addTabFields($config);
        });
    }

    protected function extendSaveDatabaseAction()
    {
        if (!class_exists(SaveDatabaseAction::class)) {
            return;
        }

        SaveDatabaseAction::extend(function ($action) {
            $action->addTableDefinition([
                'label' => 'User activity',
                'class' => UserModel::class,
                'param' => 'user'
            ]);
        });
    }

}
