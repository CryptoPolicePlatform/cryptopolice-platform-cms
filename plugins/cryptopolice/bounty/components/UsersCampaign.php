<?php namespace CryptoPolice\Bounty\Components;

use DB;
use Auth;
use Flash;
use DateTime;
use Validator;
use Cms\Classes\ComponentBase;
use CryptoPolice\Bounty\Models\BountyReport;

class UsersCampaign extends ComponentBase
{
    public $access;
    public $reportList;
    public $campaignID;
    public $profileStatistic;


    public function componentDetails()
    {
        return [
            'name' => 'Users Campaign',
            'description' => 'Users Campaign Details'
        ];
    }

    public function onRun()
    {

        $user = Auth::getUser();


        $this->campaignID = $this->param('id');


        // Get users report list
        $reports = $this->onFilterReprots();
        $this->reportList = $reports;


        // Get users statistic
        $this->profileStatistic = [
            'reward_sum' => $reports->sum->given_reward,
            'pending' => $reports->where('status', 0)->count(),
            'approved' => $reports->where('status', 1)->count(),
            'disapproved' => $reports->where('status', 2)->count(),
            'report_count' => $reports->count(),
        ];


        // Check if user is registered in current Bounty Campaign
        if (!empty($this->param('slug'))) {
            $access = $user->bountyCampaigns()->wherePivot('bounty_campaigns_id', $this->param('id'))->first();
            $this->access = $access ? $access->pivot->approval_type : null;
        }

    }


    public function onFilterReprots()
    {

        $user = Auth::getUser();
        $type = post('campaing_type');
        $status = post('status');

        if ($type) {

            $this->reportList = BountyReport::select('cryptopolice_bounty_user_reports.*', 'cryptopolice_bounty_campaigns.title as bounty_title')
                ->join('cryptopolice_bounty_campaigns', 'cryptopolice_bounty_user_reports.bounty_campaigns_id', '=', 'cryptopolice_bounty_campaigns.id')
                ->where('user_id', $user->id)
                ->where('cryptopolice_bounty_campaigns.id', $type)
                ->orderBy('created_at', 'desc')
                ->get();

        } elseif ($status) {

            $this->reportList = BountyReport::select('cryptopolice_bounty_user_reports.*', 'cryptopolice_bounty_campaigns.title as bounty_title')
                ->join('cryptopolice_bounty_campaigns', 'cryptopolice_bounty_user_reports.bounty_campaigns_id', '=', 'cryptopolice_bounty_campaigns.id')
                ->where('user_id', $user->id)
                ->where('cryptopolice_bounty_user_reports.status', $status)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            return BountyReport::select('cryptopolice_bounty_user_reports.*', 'cryptopolice_bounty_campaigns.title as bounty_title')
                ->join('cryptopolice_bounty_campaigns', 'cryptopolice_bounty_user_reports.bounty_campaigns_id', '=', 'cryptopolice_bounty_campaigns.id')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }
    }


    public function onAddReport()
    {

        $json = [];
        $user = Auth::getUser();
        $data = input();

        foreach ($data as $key => $value) {
            if ($key != 'id') {
                array_push($json, ['title' => $key, 'value' => $value]);
            }
        }

        BountyReport::insert([
                'description' => json_encode($json),
                'bounty_campaigns_id' => post('id'),
                'created_at' => new DateTime(),
                'title' => post('title'),
                'user_id' => $user->id,
            ]
        );
        Flash::success('Report successfully sent');

    }

    public function onCampaignRegistration()
    {

        $json = [];
        $data = input();
        $user = Auth::getUser();

        foreach ($data as $key => $value) {
            if ($key != 'id') {
                array_push($json, ['title' => $key, 'value' => $value]);
            }
        }

        $user->bountyCampaigns()->attach(post('id'), [
            'fields_data' => json_encode($json),
            'created_at' => new DateTime(),
            'status' => 0,
        ]);
        $user->save();
        Flash::success('Successfully registered');
    }
}
