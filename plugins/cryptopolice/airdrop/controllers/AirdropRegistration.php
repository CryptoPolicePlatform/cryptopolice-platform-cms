<?php namespace cryptopolice\airdrop\Controllers;

use Backend\Classes\Controller;
use CryptoPolice\Platform\Models\Notification;
use RainLab\User\Models\User;
use Mail;
use Carbon\Carbon;
use Flash;
use BackendMenu;

class AirdropRegistration extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController',        'Backend\Behaviors\FormController'    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('cryptopolice.airdrop', 'main-menu-item', 'side-menu-item');
    }

    public function formAfterSave($model)
    {
        $this->sendMail($model);

        if ($model->approval_type) {
            $this->addUsersNotification($model, 'Thank you for your participating in CryptoPolice AirDrop', 'Your registration was approved');
        } else {
            $this->addUsersNotification($model, 'Thank you for your participating in CryptoPolice AirDrop', $model->message);
        }
    }

    public function sendMail($model)
    {
        $user = User::where('id', $model->user_id)->first();

        $vars = [
            'name' => $user->nickname,
            'mail' => $user->email
        ];

        if ($model->approval_type) {

            Mail::send('cryptopolice.airdrop::mail.registration', $vars, function ($message) use ($user) {
                $message->to($user->email, $user->nickname)->subject('Airdrop');
            });

        } else {

            Mail::send('cryptopolice.airdrop::mail.registration_disapproved', $vars, function ($message) use ($user) {
                $message->to($user->email, $user->nickname)->subject('Airdrop');
            });
        }

        Flash::success('Mail for [' . $user->email . '] has been send');
    }


    public function addUsersNotification($model, $message, $text)
    {
        $user = User::where('id', $model->user_id)->first();

        $notify = new Notification();
        $notify->user_id            = $user->id;
        $notify->title              = $message;
        $notify->description        = $text;
        $notify->announcement_at    = Carbon::now();
        $notify->save();
    }
}
