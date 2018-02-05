<?php namespace CryptoPolice\Bounty\Components;

use CryptoPolice\Bounty\Models\BountyRegistration;
use DB;
use Auth;
use Flash;
use DateTime;
use Validator;
use Cms\Classes\ComponentBase;
use CryptoPolice\Bounty\Models\Bounty;

class UsersCampaign extends ComponentBase
{
	public $access;
	public $status;
	public $rewards;
	public $reportList;
	public $campaignID;
	public $registeredList;
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

        $this->campaignID = $this->param('id');
        $this->profileStatistic = $this->getUsersStats();

        if (!empty($this->param('slug'))) {

            $this->getUsersAccess();

            $this->campaignReports = $this->getCampaignReports();
        } else {
            $this->reportList = $this->getUsersReports();
            $this->registeredList = $this->getCampaigns();
        }
    }

    public function getCampaigns () {

	    $user = Auth::getUser();
	    return BountyRegistration::where('user_id', $user->id)->get();

    }

	public function onFilterCampaignReports()
	{

        $arr = [
            post('status')
        ];

        $this->campaignReports = DB::table('cryptopolice_bounty_user_reports')
            ->select('cryptopolice_bounty_rewards.reward_type as type', 'cryptopolice_bounty_campaigns.title as campaign_title', 'cryptopolice_bounty_campaigns.*', 'cryptopolice_bounty_user_reports.*')
            ->join('cryptopolice_bounty_campaigns', 'cryptopolice_bounty_user_reports.bounty_campaigns_id', '=', 'cryptopolice_bounty_campaigns.id')
            ->join('cryptopolice_bounty_rewards', 'cryptopolice_bounty_user_reports.reward_id', '=', 'cryptopolice_bounty_rewards.id')
            ->where('cryptopolice_bounty_campaigns.id', $this->param('id'))
            ->Where(function ($query) use ($arr) {
                if (!empty($arr[0])) {
                    $query->where('cryptopolice_bounty_user_reports.report_status', $arr[0]);
                }
            })->get();
	}

    public function getUsersStats()
    {

        $user = Auth::getUser();
        $data = DB::table('cryptopolice_bounty_user_reports')
            ->select('cryptopolice_bounty_rewards.reward_type as type', 'cryptopolice_bounty_campaigns.title as campaign_title', 'cryptopolice_bounty_campaigns.*', 'cryptopolice_bounty_user_reports.*')
            ->join('cryptopolice_bounty_campaigns', 'cryptopolice_bounty_user_reports.bounty_campaigns_id', '=', 'cryptopolice_bounty_campaigns.id')
            ->join('cryptopolice_bounty_rewards', 'cryptopolice_bounty_user_reports.reward_id', '=', 'cryptopolice_bounty_rewards.id')
            ->where('cryptopolice_bounty_user_reports.user_id', $user->id)
            ->get();

        $buf = [];
        foreach ($data as $key => $value) {
            if ($value->type == 1) {
                array_push($buf, $value->campaign_title);
            }
        }

        $stakesList = [];
        foreach (array_unique($buf) as $key => $value) {
            array_push($stakesList, [
                'campaign_title'    => $value,
                'stake_amount'      => $data->where('campaign_title', $value)->sum('given_reward')
            ]);
        }

        return [
            'stake_list'    => $stakesList,
            'report_count'  => $data->count(),
            'disapproved'   => $data->where('report_status', 2)->count(),
            'approved'      => $data->where('report_status', 1)->count(),
            'pending'       => $data->where('report_status', 0)->count(),
            'total_tokens'  => $data->where('type', 0)->sum('given_reward'),
        ];
    }

    public function onFilterReports()
    {

        $user = Auth::getUser();

        $arr = [
            post('campaign_type'),
            post('status')
        ];

        $this->reportList = DB::table('cryptopolice_bounty_user_reports')
            ->select('cryptopolice_bounty_rewards.reward_type as type', 'cryptopolice_bounty_campaigns.title as campaign_title', 'cryptopolice_bounty_campaigns.*', 'cryptopolice_bounty_user_reports.*')
            ->join('cryptopolice_bounty_campaigns', 'cryptopolice_bounty_user_reports.bounty_campaigns_id', '=', 'cryptopolice_bounty_campaigns.id')
            ->join('cryptopolice_bounty_rewards', 'cryptopolice_bounty_user_reports.reward_id', '=', 'cryptopolice_bounty_rewards.id')
            ->where('cryptopolice_bounty_user_reports.user_id', $user->id)
            ->where('cryptopolice_bounty_user_reports.created_at', 'asc')
            ->Where(function ($query) use ($arr) {
                for ($i = 0; $i < count($arr); $i++) {
                    if (!empty($arr[$i])) {
                        if ($i == 0) {
                            $query->where('cryptopolice_bounty_user_reports.bounty_campaigns_id', $arr[$i]);
                        } else {
                            $query->where('cryptopolice_bounty_user_reports.report_status', $arr[$i]);
                        }
                    }
                }
            })->get();
    }

	public function getUsersReports()
	{
		$user = Auth::getUser();
		return DB::table('cryptopolice_bounty_user_reports')
            ->select('cryptopolice_bounty_rewards.reward_type as type', 'cryptopolice_bounty_campaigns.title as campaign_title', 'cryptopolice_bounty_campaigns.*', 'cryptopolice_bounty_user_reports.*')
            ->join('cryptopolice_bounty_campaigns', 'cryptopolice_bounty_user_reports.bounty_campaigns_id', '=', 'cryptopolice_bounty_campaigns.id')
            ->join('cryptopolice_bounty_rewards', 'cryptopolice_bounty_user_reports.reward_id', '=', 'cryptopolice_bounty_rewards.id')
            ->where('cryptopolice_bounty_user_reports.user_id', $user->id)
            ->orderBy('cryptopolice_bounty_user_reports.created_at', 'desc')
            ->get();
	}

	public function getCampaignReports()
	{
        return DB::table('cryptopolice_bounty_user_reports')
            ->select('cryptopolice_bounty_rewards.reward_type as type', 'cryptopolice_bounty_campaigns.title as campaign_title', 'cryptopolice_bounty_campaigns.*', 'cryptopolice_bounty_user_reports.*')
            ->join('cryptopolice_bounty_campaigns', 'cryptopolice_bounty_user_reports.bounty_campaigns_id', '=', 'cryptopolice_bounty_campaigns.id')
            ->join('cryptopolice_bounty_rewards', 'cryptopolice_bounty_user_reports.reward_id', '=', 'cryptopolice_bounty_rewards.id')
            ->where('cryptopolice_bounty_campaigns.id', $this->param('id'))
            ->get();
	}

	public function getUsersAccess()
	{
		$user = Auth::getUser();
		$access =  $user->bountyCampaigns()->wherePivot('bounty_campaigns_id', $this->param('id'))->first();
		$this->access = $access ? $access->pivot->approval_type : null;
		$this->status = $access ? $access->pivot->status : null;
	}

	public function prepareValidationRules($query, $actionType) {


		$data = input();

  	    // create array of validation rules
		foreach ($query->fields as $key => $value) {
			if($value['action_type'] == $actionType) {
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
			return true;
		}
	}

	public function onAddReport()
	{

		$json = [];
		$user = Auth::getUser();
		$registrationData = $user->bountyCampaigns()->wherePivot('bounty_campaigns_id', $this->param('id'))->first();

		if($this->prepareValidationRules($registrationData, 'report')) {

		    // check if user has access to report
			if($registrationData->pivot->approval_type == 1 && $registrationData->pivot->status == 1) {

				// create json from input data
				foreach (input() as $key => $value) {
					if ($key != 'id') {
						array_push($json, ['title' => $key, 'value' => $value]);
					}
				}

				$user->bountyReports()->attach(post('id'), [
					'bounty_user_registration_id' => $registrationData->pivot->id,
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
		$user = Auth::getUser();
		$registrationData = Bounty::where('id', $this->param('id'))->first();

		if($this->prepareValidationRules($registrationData, 'registration')) {

			$access = $user->bountyCampaigns()->wherePivot('bounty_campaigns_id', $this->param('id'))->get();
			if ($access->isEmpty()) {

				foreach (input() as $key => $value) {
					if ($key != 'id') {
						array_push($json, ['title' => $key, 'value' => $value]);
					}
				}

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