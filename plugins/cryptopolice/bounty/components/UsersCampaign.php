<?php namespace CryptoPolice\Bounty\Components;

use CryptoPolice\Bounty\Models\Bounty;
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
    public $status;
    public $reportList;
    public $campaignID;
    public $campaignReports;
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
        // TODO :: Reports Mails (one mail each week)

        $this->campaignID = $this->param('id');

        $this->reportList = $this->getAllUsersReports();

        $this->profileStatistic = [
            'report_count' => $this->reportList->count(),
            'reward_sum' => $this->reportList->sum->given_reward,
            'disapproved' => $this->reportList->where('report_status', 2)->count(),
            'approved' => $this->reportList->where('report_status', 1)->count(),
            'pending' => $this->reportList->where('report_status', 0)->count(),
        ];

        // Check if user is registered in current Bounty Campaign
        if (!empty($this->param('slug'))) {

            $access = $this->getAccess();
            $this->access = $access ? $access->pivot->approval_type : null;
            $this->status = $access ? $access->pivot->status : null;
            $this->campaignReports = $this->getAllCampaignReports();
        }

    }

    public function onFilterCampaignReports()
    {

        if (post('status') && !empty(post('status'))) {

            $this->campaignReports = Bounty::with([
                'bountyReports' => function ($query) {
                    return $query->where('report_status', post('status'));
                }
            ])->first();

        } else {
            $this->campaignReports = Bounty::with('bountyReports')->find($this->param('id'));
        }
    }

    public function onFilterReports()
    {

        $user = Auth::getUser();

        if (post('campaign_type') && !empty(post('campaign_type'))) {
            $this->reportList = $user->bountyReports()
                ->wherePivot('bounty_campaigns_id', post('campaign_type'))
                ->get();
        } elseif (post('status')) {
            $this->reportList = $user->bountyReports()
                ->wherePivot('report_status', post('status'))
                ->get();
        } else {
            $this->reportList = $this->getAllUsersReports();
        }
    }

    public function getAllUsersReports()
    {
        $user = Auth::getUser();
        return $user->bountyReports()->withPivot('report_status')->get();
    }

    public function getAllCampaignReports()
    {
        return Bounty::with('bountyReports')->find($this->param('id'));
    }

    public function getAccess()
    {
        $user = Auth::getUser();
        return $user->bountyCampaigns()->wherePivot('bounty_campaigns_id', $this->param('id'))->first();
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
        return redirect()->back();
    }

    public function onCampaignRegistration()
    {

        $json = [];
        $data = input();
        $user = Auth::getUser();

        foreach ($data as $key => $value) {
            if ($key != 'id') {
                array_push($json, ['title' => $key, 'value' => $value]);
                $rules[$key] = 'required';
                $input[$key] = $value;
            }
        }

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $messages = $validator->messages();
            foreach ($messages->all() as $message) {
                Flash::error($message);
            }
        } else {

            $access = $user->bountyCampaigns()->wherePivot('bounty_campaigns_id', $this->param('id'))->get();
            dd($access);
            if ($access->isEmpty()) {

                $user->bountyCampaigns()->attach(post('id'), [
                    'fields_data' => json_encode($json),
                    'created_at' => new DateTime(),
                    'status' => 1,
                ]);

                $user->save();
                Flash::success('Successfully registered');
                return redirect()->back();

            } else {
                Flash::warning('You are already registered');
            }
        }
    }
}
