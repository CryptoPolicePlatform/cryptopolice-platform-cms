<?php namespace CryptoPolice\Bounty\Components;

use DB;
use Auth;
use Flash;
use Session;
use Redirect;
use DateTime;
use Validator;
use RainLab\User\Models\User;
use Cms\Classes\ComponentBase;
use CryptoPolice\Bounty\Models\Bounty;
use CryptoPolice\Platform\Models\Notification;
use CryptoPolice\Academy\Components\Recaptcha;
use CryptoPolice\Bounty\Models\BountyRegistration;

class UsersCampaign extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Users Campaign',
            'description' => 'Users Campaign Details'
        ];
    }


    public function onRun()
    {

        $this->page['campaignID'] = $this->param('id');
        $this->page['profileStatistic'] = $this->getProfileStatistic();

        if (!empty($this->param('slug'))) {
            if($this->checkBountyStatus()) {
                $this->getUsersAccess();
                $this->getRegisteredUsersCount();
                $this->page['campaignReports'] = $this->getCampaignReports();
            } else {
                return Redirect::to('/bounty-campaign');
            }
        } else {
            $this->page['registeredUsersCampaign'] = $this->getRegisteredUsersCampaign();
            $this->page['usersReports'] = $this->getUsersReports();
        }
    }

    public function checkBountyStatus() {
        return Bounty::where('slug',$this->param('slug'))
            ->where('id',$this->param('id'))
            ->value('status');
    }

    public function getBountyCamapign() {

    }

    public function getRegisteredUsersCount()
    {

        $totalUserCampaignCount = BountyRegistration::where('bounty_campaigns_id', $this->param('id'))->count('user_id');
        $totalUserCount = User::count('id');

        if ($totalUserCampaignCount) {
            $percentage = 100 / $totalUserCount * $totalUserCampaignCount;
        } else {
            $percentage = 0;
        }

        $this->page['totalRegisteredInCampaign'] = $totalUserCampaignCount;
        $this->page['bountyPercentage'] = $percentage;
    }


    public function getRegisteredUsersCampaign()
    {

        $user = Auth::getUser();
        return BountyRegistration::where('user_id', $user->id)->get();
    }


    public function onFilterCampaignReports()
    {

        $this->page['campaignReports'] = DB::table('cryptopolice_bounty_user_reports')
            ->select('cryptopolice_bounty_rewards.reward_type as type', 'cryptopolice_bounty_campaigns.title as campaign_title', 'cryptopolice_bounty_campaigns.*', 'cryptopolice_bounty_user_reports.*')
            ->join('cryptopolice_bounty_campaigns', 'cryptopolice_bounty_user_reports.bounty_campaigns_id', '=', 'cryptopolice_bounty_campaigns.id')
            ->join('cryptopolice_bounty_rewards', 'cryptopolice_bounty_user_reports.reward_id', '=', 'cryptopolice_bounty_rewards.id')
            ->where('cryptopolice_bounty_campaigns.id', $this->param('id'))
            ->Where(function ($query) {
                if (!empty(post('status'))) {
                    $query->where('cryptopolice_bounty_user_reports.report_status', post('status'));
                }
            })->get();
    }


    public function getProfileStatistic()
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
                'campaign_title' => $value,
                'stake_amount' => $data->where('campaign_title', $value)->sum('given_reward')
            ]);
        }

        $counter = $data->count();
        $approved = $data->where('report_status', 1)->count();
        $pending = $data->where('report_status', 0)->count();

        if ($counter - $pending && $approved) {
            $value = (100 / ($counter - $pending) * $approved) / 100;
        } else {
            $value = 0;
        }

        return [
            'report_percentage' => $value,
            'total_tokens' => $data->where('type', 0)->sum('given_reward'),
            'report_count' => $data->count(),
            'disapproved' => $data->where('report_status', 2)->count(),
            'approved' => $data->where('report_status', 1)->count(),
            'pending' => $data->where('report_status', 0)->count(),
            'stake_list' => $stakesList,
        ];
    }


    public function onFilterReports()
    {

        if (input('_token') == Session::token()) {

            $user = Auth::getUser();

            $arr = [
                post('campaign_type'),
                post('status')
            ];

            $this->page['usersReports'] = DB::table('cryptopolice_bounty_user_reports')
                ->select('cryptopolice_bounty_rewards.reward_type as type', 'cryptopolice_bounty_campaigns.title as campaign_title', 'cryptopolice_bounty_campaigns.*', 'cryptopolice_bounty_user_reports.*')
                ->join('cryptopolice_bounty_campaigns', 'cryptopolice_bounty_user_reports.bounty_campaigns_id', '=', 'cryptopolice_bounty_campaigns.id')
                ->join('cryptopolice_bounty_rewards', 'cryptopolice_bounty_user_reports.reward_id', '=', 'cryptopolice_bounty_rewards.id')
                ->where('cryptopolice_bounty_user_reports.user_id', $user->id)
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
                })
                ->orderBy('cryptopolice_bounty_user_reports.created_at', 'asc')
                ->get();
        }
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
            ->orderBy('cryptopolice_bounty_campaigns.created_at', 'desc')
            ->get();
    }


    public function getUsersAccess()
    {

        $user = Auth::getUser();
        $query = $user->bountyCampaigns()->where('cryptopolice_bounty_user_registration.deleted_at', null)->wherePivot('bounty_campaigns_id', $this->param('id'))->first();
        $this->page['btc_code'] = $query ? $query->pivot->btc_code : null;
        $this->page['access']   = $query ? $query->pivot->approval_type : null;
        $this->page['status']   = $query ? $query->pivot->status : null;
    }


    public function prepareValidationRules($query, $actionType)
    {

        $data = input();

        // create array of validation rules
        foreach ($query->fields as $key => $value) {
            if ($value['action_type'] == $actionType) {
                $rules[$value['name']] = $value['regex'];
            }
        }

        // check validation
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            Flash::error($validator->messages()->first());
        } else {
            return true;
        }
    }


    public function onAddReport()
    {

        Recaptcha::verifyCaptcha();

        if (input('_token') == Session::token()) {

            $json = [];
            $user = Auth::getUser();
            $registrationData = $user->bountyCampaigns()->wherePivot('bounty_campaigns_id', $this->param('id'))->first();

            if ($this->prepareValidationRules($registrationData, 'report')) {

                // check if user has access to report
                if ($registrationData->pivot->approval_type == 1 && $registrationData->pivot->status == 1) {

                    // create json from input data
                    foreach (input() as $key => $value) {
                        if ($key != 'id' && $key != 'g-recaptcha-response' && $key != '_session_key' && $key != '_token') {
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
    }

    public function generateBountyCode()
    {
        $code = 'OFC-'.mb_strtoupper(md5(uniqid(rand(),true)));
        $query = BountyRegistration::where('btc_code', $code)->get();
        return $query->isNotEmpty() ? $this->generateBountyCode() : $code;
    }

    public function onCampaignRegistration()
    {

        Recaptcha::verifyCaptcha();

        if (input('_token') == Session::token()) {

            $json = [];
            $user = Auth::getUser();
            $registrationData = Bounty::where('id', $this->param('id'))->first();

            if ($this->prepareValidationRules($registrationData, 'registration')) {

                $access = $user->bountyCampaigns()->where('cryptopolice_bounty_user_registration.deleted_at', null)->wherePivot('bounty_campaigns_id', $this->param('id'))->get();
                if ($access->isEmpty()) {

                    foreach (input() as $key => $value) {
                        if ($key != 'id' && $key != 'g-recaptcha-response' && $key != '_session_key' && $key != '_token') {
                            array_push($json, ['title' => $key, 'value' => $value]);
                        }
                    }

                    $code = $this->generateBountyCode();

                    $user->bountyCampaigns()->attach(post('id'), [
                        'btc_code' => $code,
                        'btc_username' => input('bitcointalk_username'),
                        'fields_data' => json_encode($json),
                        'created_at' => new DateTime(),
                        'status' => 1,
                    ]);
                    $user->save();

                    $this->setUserNotification($user->id, $code);

                    Flash::success('Successfully registered');
                    return redirect()->back();

                } else {
                    Flash::warning('You are already registered');
                }
            }
        }
    }

    public function setUserNotification($userID, $code)
    {
        $notify = new Notification();
        $notify->user_id = $this->user_id;
        $notify->title = 'Registration in CryptoPolice bounty campaign';
        $notify->description = 'To verify your registration please approve your Bitcointalk account <br> Post this message to our Bitcointalk bounty announcement <br><a href="">LINK</a><br>Message:<br>I registered to CryptoPolice bounty campaign My registration code is '.$code;
        $notify->user_id = $userID;
        $notify->save();
    }
}