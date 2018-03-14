<?php namespace CryptoPolice\Bounty\Controllers;

use Carbon\Carbon;
use Mail;
use Flash;
use BackendMenu;
use RainLab\User\Models\User;
use Backend\Classes\Controller;
use CryptoPolice\Bounty\Models\Bounty;
Use CryptoPolice\Platform\Models\Notification;

class BountyReports extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController'
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('CryptoPolice.Bounty', 'bounty-campaign', 'users-bounties');
    }


    public function formAfterSave($model)
    {
        $this->sendMail($model);
        $this->addUsersNotification($model);
    }


    public function addUsersNotification($model)
    {
        $campaign = Bounty::where('id', $model->bounty_campaigns_id)->first();

        $notify = new Notification();
        $notify->user_id = $model->user_id;
        $notify->title = 'Your ' . $campaign->title . ' Bounty campaign report was reviewed!';
        $notify->description = 'For more information please go to your CryptoPolice Bounty campaign profile.';
        $notify->announcement_at = Carbon::now();
        $notify->save();
    }

    public function sendMail($model)
    {

        $user = User::where('id', $model->user_id)->first();
        $campaign = Bounty::where('id', $model->bounty_campaigns_id)->first();

        $vars = [
            'name' => $user->nickname,
            'mail' => $user->email,
            'campaignTitle' => $campaign->title,
        ];
        Mail::send('cryptopolice.bounty::mail.report', $vars, function ($message) use ($user) {
            $message->to($user->email, $user->full_name)->subject('Bounty Campaign Report');
        });
        Flash::success('REPORT mail & notification for [' . $user->email . '] has been send');
    }
}
