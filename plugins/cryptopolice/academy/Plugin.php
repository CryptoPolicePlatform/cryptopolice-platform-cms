<?php namespace CryptoPolice\Academy;

use Auth;
use Event;
use Redirect;
use ValidationException;
use System\Classes\PluginBase;
use RainLab\User\Models\User as UserModel;
use RainLab\User\Controllers\Users as UsersController;
use CryptoPolice\Academy\Components\Recaptcha as Recaptcha;

class Plugin extends PluginBase
{

    public $require = [
        'VojtaSvoboda.TwigExtensions',
        'RainLab.Location',
        'Netsti.Uploader',
        'RainLab.Notify',
        'RainLab.User',
    ];

    public function registerComponents()
    {
        return [
            'CryptoPolice\Academy\Components\Exams'            => 'Exams',
            'CryptoPolice\Academy\Components\ExamTask'         => 'ExamTask',
            'CryptoPolice\Academy\Components\Recaptcha'        => 'reCaptcha',
            'CryptoPolice\Academy\Components\Trainings'        => 'Trainings',
            'CryptoPolice\Academy\Components\ProfileForm'      => 'ProfileForm',
            'CryptoPolice\Academy\Components\TrainingTask'     => 'TrainingTask',
            'CryptoPolice\Academy\Components\CustomUploader'   => 'CustomUploader',
        ];
    }

    public function registerSettings()
    {

    }

    /*
     *  UserModel::extend(function($model) {

            $model->hasMany['instruments'] = ['Cunami\InventoryManagement\Models\InstrumentsHoldersTransfere', 'key' => 'holder_id'];

        });
     */
    public function boot()
    {

        $this->extendUserModel();
        $this->extendUsersController();

        // For registration form

        Event::listen('rainlab.user.beforeRegister', function () {

            Recaptcha::verifyCaptcha();

            $userPassword = post('password');
            if (!preg_match('/[a-zA-Z]/', $userPassword)) {
                throw new ValidationException([
                    'password' => 'Password should contain at least one letter character'
                ]);
            }

        });

        // For login form

        Event::listen('rainlab.user.beforeAuthenticate', function () {

            Recaptcha::verifyCaptcha();

        });

    }

    protected function extendUserModel()
    {
        UserModel::extend(function ($model) {

            $model->addFillable([
                'eth_address'
            ]);

            $model->belongsToMany['bountyCampaigns'] = ['CryptoPolice\Bounty\Models\Bounty',
                'table'     => 'cryptopolice_bounty_user_registration',
                'pivot'     => ['approval_type', 'status'],
                'otherKey'  => 'bounty_campaigns_id',
                'key'       => 'user_id'
            ];

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
}