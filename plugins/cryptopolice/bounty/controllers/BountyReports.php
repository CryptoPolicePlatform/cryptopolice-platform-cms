<?php namespace CryptoPolice\Bounty\Controllers;

use Mail;
use Flash;
use Exception;
use BackendMenu;
use Carbon\Carbon;
use RainLab\User\Models\User;
use Backend\Classes\Controller;
use CryptoPolice\Bounty\Models\Bounty;
use CryptoPolice\Academy\Models\Settings;
use CryptoPolice\Bounty\Models\BountyReport;
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

    public function onUpdateReportsStatus()
    {

        $settings   = Settings::instance();
        $idList     = post('BountyReport.report_list');

        try {
            if ($settings->campaign_reports_group) {
                foreach (json_decode($idList) as $value) {
                    BountyReport::where('id', $value->id)
                        ->where('created_at', '>=', $settings->campaign_reports_start_date)
                        ->where('created_at', '<=', $settings->campaign_reports_end_date)
                        ->update([
                            'report_status' => '2'
                        ]);
                }
                Flash::warning('All users reports was successfully BLOCKED');
            } else {
                Flash::error('Group setting is not selected');
            }
        } catch (Exception $e) {
            Flash::error($e->getMessage());
        }
    }

    public function onVerifyReport($modal)
    {

        $arr = [];
        $settings = Settings::instance();

        $report = BountyReport::find($modal);
        $allReports = BountyReport::where('id', '!=', $modal)->where('created_at', '<=', $report->created_at)->get();

        if (isset($report->description) && !empty($report->description)) {
            foreach ($report->description as $userReport) {

                if (isset($allReports) && !empty($allReports)) {
                    foreach ($allReports as $report) {

                        if (isset($report->description) && !empty($report->description)) {
                            foreach ($report->description as $usersReport) {

                                if (array_search($usersReport['title'], ['retweet', 'tweet', 'link_post_or_share', 'link_post', 'bitcointalk_link'], true)) {
                                    if ($userReport['value'] == $usersReport['value'] && !empty($usersReport['value'])) {
                                        array_push($arr, ['id' => $report->id, 'created_at' => $report->created_at, 'duplicate' => $usersReport['value']]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return [
            'result' => $this->makePartial('verify', ['arr' => $arr])
        ];
    }
}
