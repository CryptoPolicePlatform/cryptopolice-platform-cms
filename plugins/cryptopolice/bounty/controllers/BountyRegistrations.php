<?php namespace CryptoPolice\Bounty\Controllers;

use Carbon\Carbon;
use Mail;
use Flash;
use BackendMenu;
use RainLab\User\Models\User;
use Backend\Classes\Controller;
use CryptoPolice\Bounty\Models\Bounty;
use CryptoPolice\Platform\Models\Notification;

class BountyRegistrations extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\ReorderController'
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $reorderConfig = 'config_reorder.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('CryptoPolice.Bounty', 'bounty-campaign', 'campaigns-registration');
    }

    public function formAfterSave($model)
    {

        if (!empty($model->message) && $model->reverified) {

            $user       = User::where('id', $model->user_id)->first();
            $campaign   = Bounty::where('id', $model->bounty_campaigns_id)->first();

            $notify = new Notification();

            $notify->user_id            = $user->id;
            $notify->title              = '[Need your action] Notification from admin regarding CryptoPolice ' . $campaign->title . ' bounty campaign';
            $notify->description        = $model->message;
            $notify->announcement_at    = Carbon::now();

            $notify->save();

        }

        if ($model->approval_type && $model->btc_status) {
            $this->addUsersNotification($model);
            $this->sendMail($model);
        }

    }

    public function addUsersNotification($model)
    {
        $user = User::where('id', $model->user_id)->first();
        $campaign = Bounty::where('id', $model->bounty_campaigns_id)->first();

        $notify = new Notification();
        $notify->user_id            = $user->id;
        $notify->title              = 'Thank you for your registration in CryptoPolice ' . $campaign->title . ' bounty campaign';
        $notify->description        = 'Now you can make you reports basing on the conditions of the campaign';
        $notify->announcement_at    = Carbon::now();
        $notify->save();
    }

    public function sendMail($model)
    {
        $user = User::where('id', $model->user_id)->first();

        $vars = [
            'name' => $user->nickname,
            'mail' => $user->email
        ];

        Mail::send('cryptopolice.bounty::mail.registration', $vars, function ($message) use ($user) {
            $message->to($user->email, $user->nickname)->subject('Bounty Campaign Registration');
        });
        Flash::success('REGISTRATION Mail & notification for [' . $user->email . '] has been send');
    }
}
