<?php namespace CryptoPolice\Bounty\Components;

use DB;
use Auth;
use Flash;
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
        $reports = BountyReport::select('cryptopolice_bounty_user_reports.*', 'cryptopolice_bounty_campaigns.title as bounty_title')
            ->join('cryptopolice_bounty_campaigns', 'cryptopolice_bounty_user_reports.bounty_campaigns_id', '=', 'cryptopolice_bounty_campaigns.id')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        $this->reportList = $reports;

        // Get users statistic
        $this->profileStatistic = [
            'reward_sum' => $reports->sum->given_reward,
            'approved' => $reports->where('status', 0)->count(),
            'pending' => $reports->where('status', 1)->count(),
            'disapproved' => $reports->where('status', 2)->count(),
            'report_count' => $reports->count(),
        ];

        // Check if user is registered in current Bounty Campaign
        if (!empty($this->param('slug'))) {
            $access = $user->bountyCampaigns()->wherePivot('bounty_campaigns_id', $this->param('id'))->first();
            $this->access = $access ? $access->pivot->approval_type : null;
        }

    }

    public function onAddReport()
    {

        $user = Auth::getUser();

        $rules = [
            'title' => 'required',
            'description' => 'required',
        ];

        $validator = Validator::make(post(), $rules);

        if ($validator->fails()) {

            $messages = $validator->messages();
            foreach ($messages->all() as $message) {
                Flash::error($message);
            }

        } else {

            BountyReport::insert([
                    'description' => post('description'),
                    'bounty_campaigns_id' => post('id'),
                    'title' => post('title'),
                    'user_id' => $user->id,
                    'status' => 0,
                ]
            );

            Flash::success('Report successfully sent');
        }
    }

    public function onCampaignRegistration()
    {

        $json  = [];
        $data = input();
        $user = Auth::getUser();

        foreach ($data as $key => $value) {
            if ($key != 'id') {
                array_push($json , ['title' => $key, 'value' => $value]);
            }
        }

        $user->bountyCampaigns()->attach(post('id'), ['fields_data' => json_encode($json )]);
        $user->save();

        Flash::success('Successfully registered');


    }
}
