<?php namespace CryptoPolice\Bounty\Components;

use CryptoPolice\Bounty\Models\Bounty;
use DB;
use Auth;
use Flash;
use DateTime;
use Validator;
use Cms\Classes\ComponentBase;
use CryptoPolice\Bounty\Models\BountyReport;
use CryptoPolice\Bounty\Models\Reward;

class UsersCampaign extends ComponentBase
{
	public $access;
	public $status;
	public $rewards;
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

        // TODO : Reports Mails (one mail each week)
		$this->campaignID = $this->param('id');
		$this->getUsersReports();
        $this->profileStatistic = $this->getUsersStats();

		if (!empty($this->param('slug'))) {
			$this->getUsersAccess();
			$this->getCampaingReports();
		}
	}


//	public function getUsersStats()
//	{
//
//		$sum = 0;
//		$counter = 0;
//		$pendingCounter = 0;
//		$approvedCounter = 0;
//		$disapprovedCounter = 0;
//
//		foreach ($this->reportList as $val) {
//
//			$counter += 1;
//			$sum += $val->pivot->given_reward;
//
//			if ($val->pivot->report_status == 0) {
//				$pendingCounter += 1;
//			} elseif ($val->pivot->report_status == 1) {
//				$approvedCounter += 1;
//			} elseif ($val->pivot->report_status == 2) {
//				$disapprovedCounter += 1;
//			}
//		}
//
//		$this->profileStatistic = [
//			'disapproved'       => $disapprovedCounter,
//			'approved'          => $approvedCounter,
//			'pending'           => $pendingCounter,
//			'report_count'      => $counter,
//			'reward_sum'        => $sum,
//		];
//	}

	public function onFilterCampaignReports()
	{
        // TODO : fix filter
		if (post('status') && !empty(post('status'))) {
			$this->campaignReports = Bounty::with('bountyReports')->find($this->param('id'));
		} else {
			$this->campaignReports = Bounty::with('bountyReports')->find($this->param('id'));
		}
	}

    public function getUsersStats()
    {

        $user = Auth::getUser();
        $this->rewards = Reward::get();
        $data = BountyReport::join('cryptopolice_bounty_campaigns', 'cryptopolice_bounty_user_reports.bounty_campaigns_id', '=', 'cryptopolice_bounty_campaigns.id')
            ->join('cryptopolice_bounty_rewards', 'cryptopolice_bounty_user_reports.reward_id', '=', 'cryptopolice_bounty_rewards.id')
            ->where('cryptopolice_bounty_user_reports.user_id', $user->id)
            ->get();


        $buf = [];
        foreach($data as $key => $value) {
            if($value->reward_type == 1) {
                array_push($buf, $value->title);
            }
        }

        $stakesList = [];
        foreach (array_unique($buf) as $key => $value ) {

            array_push($stakesList,[
                'campaign_title'    => $value,
                'stake_amount'      => $data->where('title', $value)->sum('given_reward')
            ]);
        }

        return [
            'report_count'  => $data->count(),
            'disapproved'   => $data->where('report_status', 2)->count(),
            'approved'      => $data->where('report_status', 1)->count(),
            'pending'       => $data->where('report_status', 0)->count(),
            'total_stakes'  => $data->where('reward_type', 1)->sum('given_reward'),
            'total_tokens'  => $data->where('reward_type', 0)->sum('given_reward'),
            'stake_list'    => $stakesList,
        ];
    }

	public function onFilterReports()
	{

        // TODO : filter time
		$user = Auth::getUser();

		if (post('campaign_type') && post('status')) {

			$this->reportList = $user->bountyReports()
			->wherePivot('bounty_campaigns_id', post('campaign_type'))
			->wherePivot('report_status', post('status'))
			->get();

		} elseif (post('status')) {

			$this->reportList = $user->bountyReports()
			->wherePivot('report_status', post('status'))
			->get();

		}
		elseif (post('campaign_type')) {

			$this->reportList = $user->bountyReports()
			->wherePivot('bounty_campaigns_id', post('campaign_type'))
			->get();

		} else {
			$this->reportList = $this->getUsersReports();
		}
	}

	public function getUsersReports()
	{
		$user = Auth::getUser();
		$this->reportList = $user->bountyReports()->get();
	}

	public function getCampaingReports()
	{

		$this->campaignReports = Bounty::with('bountyReports')->find($this->param('id'));
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
		$registraionData = $user->bountyCampaigns()->wherePivot('bounty_campaigns_id', $this->param('id'))->first();

		if($this->prepareValidationRules($registraionData, 'report')) {

		    // check if user has access to report
			if($registraionData->pivot->approval_type == 1 && $registraionData->pivot->status == 1) {

				// create json from input data
				foreach (input() as $key => $value) {
					if ($key != 'id') {
						array_push($json, ['title' => $key, 'value' => $value]);
					}
				}

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
		$user = Auth::getUser();
		$registrationData = Bounty::where('id', $this->param('id'))->first();

		if($this->prepareValidationRules($registraionData, 'registration')) {

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