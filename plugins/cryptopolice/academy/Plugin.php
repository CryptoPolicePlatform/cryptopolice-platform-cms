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
        return [
            'settings' => [
                'label'       => 'Academy Plugin',
                'description' => 'CryptoPolice Academy Plugin',
                'icon'        => 'icon-users',
                'class'       => 'CryptoPolice\Academy\Models\Settings',
            ]
        ];
    }

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
                'telegram_username',
                'facebook_link',
                'youtube_link',
                'twitter_link',
                'eth_address',
                'btc_link',
            ]);

            // Extended Relations, pivot tables

            $model->belongsToMany = [

                'bountyCampaigns' => ['CryptoPolice\Bounty\Models\Bounty',
                    'table' => 'cryptopolice_bounty_user_registration',
                    'pivot' => [
                        'approval_type', 'status', 'id'
                    ],
                    'otherKey' => 'bounty_campaigns_id',
                    'key' => 'user_id'
                ],

                'bountyReports' => ['CryptoPolice\Bounty\Models\Bounty',
                    'table' => 'cryptopolice_bounty_user_reports',
                    'pivot' => [
                        'report_status', 'description', 'title', 'comment', 'fields_data','given_reward'
                    ],
                    'otherKey' => 'bounty_campaigns_id',
                    'key' => 'user_id'
                ]
            ];

            $model->hasMany = [

                'userReportList' => ['CryptoPolice\Bounty\Models\BountyReport',
                    'table' => 'bounty_users_reports',
                    'key' => 'user_id',
                ],

                'userRegistrationList' => ['CryptoPolice\Bounty\Models\BountyRegistration',
                    'table' => 'bounty_users_registration',
                    'key' => 'user_id',
                ],
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