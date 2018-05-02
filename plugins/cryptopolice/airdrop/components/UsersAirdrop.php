<?php namespace CryptoPolice\Airdrop\Components;

use Auth;
use Illuminate\Support\Facades\Redirect;
use Session;
use Validator;
use RainLab\User\Models\User;
use Cms\Classes\ComponentBase;
use October\Rain\Support\Facades\Flash;
use cryptopolice\airdrop\Models\Airdrop;
use CryptoPolice\Academy\Models\Settings;
use cryptopolice\airdrop\Models\AirdropRegistration;
use CryptoPolice\Academy\Components\Recaptcha;


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

        $this->page['airdrop'] = Airdrop::first();
        $this->page['airdrop_title'] = $settings->airdrop_title;
        $this->page['airdrop_description'] = $settings->airdrop_description;

        $user = Auth::getUser();
        if ($user) {
            $this->page['airdrop_registration'] = $user->airDropRegistration()->get();
        }


        $totalAirdropCount = AirdropRegistration::count();
        $totalUserCount = User::count();

        if ($totalAirdropCount) {
            $percentage = 100 / $totalUserCount * $totalAirdropCount;
        } else {
            $percentage = 0;
        }

        $this->page['totalAirdropRegistrations'] = $totalAirdropCount;
        $this->page['percentageAirdropRegistrations'] = $percentage;

    }

    public function onAirdropRegistration()
    {

        Recaptcha::verifyCaptcha();

        if (input('_token') == Session::token()) {

            $json = [];
            $data = Airdrop::first();

            if ($this->prepareValidationRules($data)) {

                $user = Auth::getUser();
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
                            'fields_data' => json_encode($json),
                            'airdrop_id' => 1,
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
