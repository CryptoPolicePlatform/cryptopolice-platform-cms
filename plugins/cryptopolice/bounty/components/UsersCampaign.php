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


        $registraionData = Bounty::where('id', $this->param('id'))->first();

        // create array of validation rules
        foreach ($registraionData->fields as $key => $value) {
            if($value['action_type'] == 'registration') {
                $rules[$value['name']] = $value['regex'];
            }
        }


dump( $rules);
dump( $registraionData);

        // TODO : Reports Mails (one mail each week)

        $this->campaignID = $this->param('id');
        $this->reportList = $this->getAllUsersReports();

        $sum = 0;
        $counter = 0;
        $pendingCounter = 0;
        $approvedCounter = 0;
        $disapprovedCounter = 0;

        foreach ($this->reportList as $val) {

            $counter += 1;
            $sum += $val->pivot->given_reward;

            if ($val->pivot->report_status == 0) {
                $pendingCounter += 1;
            } elseif ($val->pivot->report_status == 1) {
                $approvedCounter += 1;
            } elseif ($val->pivot->report_status == 2) {
                $disapprovedCounter += 1;
            }
        }

        $this->profileStatistic = [
            'disapproved'       => $disapprovedCounter,
            'approved'          => $approvedCounter,
            'pending'           => $pendingCounter,
            'report_count'      => $counter,
            'reward_sum'        => $sum,
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
        // TODO : fix filter
        if (post('status') && !empty(post('status'))) {
            $this->campaignReports = Bounty::with('bountyReports')->find($this->param('id'));
        } else {
            $this->campaignReports = Bounty::with('bountyReports')->find($this->param('id'));
        }
    }

    public function onFilterReports()
    {

        // TODO : filter time
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
        return $user->bountyReports()->get();
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

        $registraionData = $user->bountyCampaigns()->wherePivot('bounty_campaigns_id', $this->param('id'))->first();

        // create array of validation rules
        foreach ($registraionData->fields as $key => $value) {
            if($value['action_type'] == 'report') {
                $rules[$value['name']] = $value['regex'];
            }
        }

        // check validation 
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            $messages = $validator->messages();
            foreach ($messages->all() as $message) {
                Flash::error($message);
            }
        } else {

            // create json from input data
            foreach ($data as $key => $value) {
                if ($key != 'id') {
                    array_push($json, ['title' => $key, 'value' => $value]);
                }
            }

            // check if user has access to report
            if($registraionData->pivot->approval_type == 1 && $registraionData->pivot->status == 1) {

                $user->bountyReports()->attach(post('id'), [
                    'bounty_user_registration_id' => $registraionData->pivot->id,
                    'description' => json_encode($json),
                    'created_at' => new DateTime(),
                ]);
                $user->save();

                Flash::success('Report successfully sent');

            } else {
                Flash::error('You are not allowed to send reports');
            }
            return redirect()->back();
        }
    }

    public function onCampaignRegistration()
    {

        $json = [];
        $data = input();
        $user = Auth::getUser();

        $registraionData = Bounty::where('id', $this->param('id'))->first();

        // create array of validation rules
        foreach ($registraionData->fields as $key => $value) {
            if($value['action_type'] == 'registration') {
                $rules[$value['name']] = $value['regex'];
            }
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            $messages = $validator->messages();
            foreach ($messages->all() as $message) {
                Flash::error($message);
            }
        } else {

            foreach ($data as $key => $value) {
                if ($key != 'id') {
                    array_push($json, ['title' => $key, 'value' => $value]);
                }
            }

            $access = $user->bountyCampaigns()->wherePivot('bounty_campaigns_id', $this->param('id'))->get();
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
