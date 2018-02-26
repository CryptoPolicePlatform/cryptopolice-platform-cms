<?php namespace CryptoPolice\Academy;

use Auth;
use Event;
use Redirect;
use ValidationException;
use System\Classes\PluginBase;
use RainLab\User\Models\User as UserModel;
use CryptoPolice\Academy\Components\Recaptcha;
use LasseRafn\InitialAvatarGenerator\InitialAvatar;
use RainLab\User\Controllers\Users as UsersController;

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
            'CryptoPolice\Academy\Components\Exams'         => 'Exams',
            'CryptoPolice\Academy\Components\ExamTask'      => 'ExamTask',
            'CryptoPolice\Academy\Components\Recaptcha'     => 'reCaptcha',
            'CryptoPolice\Academy\Components\Trainings'     => 'Trainings',
            'CryptoPolice\Academy\Components\ProfileForm'   => 'ProfileForm',
            'CryptoPolice\Academy\Components\TrainingTask'  => 'TrainingTask'
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

        $this->extendUserModel();
        $this->extendUsersController();

        // Password validation before registraion

        Event::listen('rainlab.user.beforeRegister', function () {
            Recaptcha::verifyCaptcha();
            $userPassword = post('password');
            if (!preg_match('/[a-zA-Z]/', $userPassword)) {
                throw new ValidationException([
                    'password' => 'Password should contain at least one letter character'
                ]);
            }
        });

        // verify recaptcha before user try to login into paltform
        Event::listen('rainlab.user.beforeAuthenticate', function () {
            Recaptcha::verifyCaptcha();
        });

        // set users nickname as a first part of an email addres
        Event::listen('rainlab.user.register', function ($user) {

            $user->avatar = (new \System\Models\File)->fromData($this->generateAvatar($user), 'asse_Radfn.jpeg');

            $nickname = explode("@", $user->email);
            $user->update(['nickname' => $nickname[0]]);
        });

    }

    protected function generateAvatar($user)
    {

        $avatar = new InitialAvatar();

        return $avatar->name(substr($user->email, 0, 2))
            ->length(2)
            ->fontSize(0.5)
            ->size(2048)
            ->font('/fonts/OpenSans-Semibold.ttf')
            ->background('#' . dechex(rand(0, 10000000)))
            ->color('#fff')
            ->generate()
            ->stream('png', 100);
    }


    protected function extendUserModel()
    {
        UserModel::extend(function ($model) {

            // set fillable fields to User model

            $model->addFillable([
                'telegram_username',
                'facebook_link',
                'youtube_link',
                'twitter_link',
                'eth_address',
                'nickname',
                'btc_link',
            ]);

            // Extended Relations for user Model

            $model->belongsToMany['bountyCampaigns'] = [
                'CryptoPolice\Bounty\Models\Bounty',
                'table'     => 'cryptopolice_bounty_user_registration',
                'pivot'     => ['approval_type', 'status', 'btc_code', 'id'],
                'otherKey'  => 'bounty_campaigns_id',
                'key'       => 'user_id'
            ];

            $model->belongsToMany['bountyReports'] = [
                'CryptoPolice\Bounty\Models\Bounty',
                'table'     => 'cryptopolice_bounty_user_reports',
                'pivot'     => ['report_status', 'reward_id', 'description', 'title', 'comment', 'fields_data', 'given_reward', 'id'],
                'order'     => ['cryptopolice_bounty_user_reports.created_at desc'],
                'otherKey'  => 'bounty_campaigns_id',
                'key'       => 'user_id',
            ];

            $model->hasMany = [

                'userReportList' => [
                    'CryptoPolice\Bounty\Models\BountyReport',
                    'table'     => 'bounty_users_reports',
                    'key'       => 'user_id',
                ],

                'userRegistrationList' => [
                    'CryptoPolice\Bounty\Models\BountyRegistration',
                    'table'     => 'bounty_users_registration',
                    'key'       => 'user_id',
                ],

                'userCommunityPosts' => [
                    'CryptoPolice\Platform\Models\CommunityPost',
                    'table'     => 'cryptopolice_platform_community_posts',
                    'key'       => 'user_id',
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

            // Tab field for ehtereum waller address

            $widget->addTabFields([
                'eth_address' => [
                    'label' => 'Ethereum wallet address',
                    'span' => 'left',
                    'tab' => 'Personal data'
                ],
            ]);
        });
    }
}