<?php namespace CryptoPolice\Airdrop\Components;

use Auth;
use Session;
use Validator;
use RainLab\User\Models\User;
use Cms\Classes\ComponentBase;
use Illuminate\Support\Facades\DB;
use October\Rain\Support\Facades\Flash;
use Illuminate\Support\Facades\Redirect;
use cryptopolice\airdrop\Models\Airdrop;
use CryptoPolice\Academy\Models\Settings;
use CryptoPolice\Academy\Components\Recaptcha;
use cryptopolice\airdrop\Models\AirdropRegistration;

class UsersAirdrop extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Airdrop',
            'description' => 'Airdrop plugin'
        ];
    }

    public function onRun()
    {
        $settings = Settings::instance();

        $this->page['airdrop']                      = Airdrop::first();
        $this->page['airdrop_title']                = $settings->airdrop_title;
        $this->page['airdrop_description']          = $settings->airdrop_description;
        $this->page['airdrop_approved_title']       = $settings->airdrop_approved_title;
        $this->page['airdrop_registration_title']   = $settings->airdrop_registration_title;
        $this->page['airdrop_approved_description'] = $settings->airdrop_approved_description;

        $user = Auth::getUser();

        if ($user) {
            $this->page['profileStatistic']     = $this->getProfileStatistic();
            $this->page['airdrop_registration'] = $user->airDropRegistration()->first();
        }

        $this->page['totalAirdropRegistrations']        = AirdropRegistration::count();
    }

    public function getProfileStatistic()
    {

        $user = Auth::getUser();
        $data = DB::table('cryptopolice_bounty_user_reports')
            ->select('cryptopolice_bounty_rewards.reward_type as type',
                'cryptopolice_bounty_campaigns.title as campaign_title',
                'cryptopolice_bounty_campaigns.*', 'cryptopolice_bounty_user_reports.*')
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

        $counter    = $data->count();
        $approved   = $data->where('report_status', 1)->count();
        $pending    = $data->where('report_status', 0)->count();

        if ($counter - $pending && $approved) {
            $value = (100 / ($counter) * $approved - $pending) / 100;
        } else {
            $value = 0;
        }

        return [
            'report_percentage' => $value,
            'total_tokens'      => $data->where('type', 0)->sum('given_reward'),
            'report_count'      => $data->count(),
            'disapproved'       => $data->where('report_status', 2)->count(),
            'approved'          => $data->where('report_status', 1)->count(),
            'pending'           => $data->where('report_status', 0)->count(),
            'stake_list'        => $stakesList,
        ];
    }


    public function onAirdropRegistration()
    {

        Recaptcha::verifyCaptcha();

        if (input('_token') == Session::token()) {

            $json = [];
            $data = Airdrop::first();

            if ($this->prepareValidationRules($data)) {

                $user = Auth::getUser();
                $counter = AirdropRegistration::count();

                if(11764  <= $counter) {
                    Flash::error('The registration on CryptoPolice AirDrop is closed');
                    return Redirect::to('/airdrop');
                }

                $access = $user->airDropRegistration()->get();

                if ($access->isEmpty()) {

                    $registrations = AirdropRegistration::all();

                    foreach (input() as $key => $value) {
                        foreach ($registrations as $reg) {
                            foreach (json_decode($reg['fields_data']) as $field) {
                                if ($field->value == $value) {
                                    Flash::error('User with this credentials in airdrop are already registered');
                                    return Redirect::to('/airdrop');
                                }
                            }
                        }
                    }

                    foreach (input() as $key => $value) {
                        if ($key != 'id' && $key != 'g-recaptcha-response' && $key != '_session_key' && $key != '_token') {
                            array_push($json, ['title' => strip_tags($key), 'value' => strip_tags($value)]);
                        }
                    }

                    $user->airDropRegistration()->create([
                            'fields_data'   => json_encode($json),
                            'airdrop_id'    => 1,
                        ]
                    );

                    Flash::success('Successfully registered');
                    return Redirect::to('/airdrop');
                } else {
                    Flash::warning('You are already registered');
                    return Redirect::to('/airdrop');
                }
            }
        }
    }


    public function prepareValidationRules($query)
    {

        $rules = [];
        $messages = [];

        foreach ($query->fields as $value) {

            $rules[$value['name']] = $value['regex'];
        }

        // check validation
        $validator = Validator::make(input(), $rules, $messages);

        if ($validator->fails()) {
            Flash::error($validator->messages()->first());
        } else {
            return true;
        }
    }
}
