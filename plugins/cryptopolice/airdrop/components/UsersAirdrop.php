<?php namespace CryptoPolice\Airdrop\Components;

use Auth;
use Session;
use DateTime;
use Validator;
use Cms\Classes\ComponentBase;
use October\Rain\Support\Facades\Flash;
use cryptopolice\airdrop\Models\Airdrop;
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

    /**
     * Displays a list of available exams.
     * - Get exam list;
     * - Get user current scores;
     */

    public function onRun()
    {
        $this->page['airdrop'] = Airdrop::first();

        $user = Auth::getUser();
        if ($user) {
            $this->page['airdrop_registration'] = $user->airDropRegistration()->get();
        }
    }

    public function onAirdropRegistration()
    {

        // Recaptcha::verifyCaptcha();

        if (input('_token') == Session::token()) {

            $json = [];
            $data = Airdrop::first();

            if ($this->prepareValidationRules($data)) {

                $user = Auth::getUser();
                $access = $user->airDropRegistration()->get();

                if ($access->isEmpty()) {

                    foreach (input() as $key => $value) {
                        if ($key != 'id' && $key != 'g-recaptcha-response' && $key != '_session_key' && $key != '_token') {
                            array_push($json, ['title' => strip_tags($key), 'value' => strip_tags($value)]);
                        }
                    }

                    $user->airDropRegistration()->create(
                        ['fields_data' => json_encode($json)],
                        ['airdrop_id' => 1]
                    );

                    Flash::success('Successfully registered');
                    return redirect()->back();

                } else {
                    Flash::warning('You are already registered');
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
