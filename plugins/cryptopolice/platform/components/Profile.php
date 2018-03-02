<?php namespace CryptoPolice\Platform\Components;

use Auth;
use Flash;
use Input;
use Session;
use Validator;
use ValidationException;
use Cms\Classes\ComponentBase;
use CryptoPolice\Academy\Components\Recaptcha as Recaptcha;

class Profile extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Profile Form',
            'description' => 'Users profile form'
        ];
    }

    public function onUpdateProfile()
    {

        Recaptcha::verifyCaptcha();

        if (input('_token') == Session::token()) {

            $user = Auth::getUser();
            $user->update(post());
            Flash::success('Profile has been successfully updated');
        }
    }

    public function onUpdateWalletAddress()
    {

        Recaptcha::verifyCaptcha();

        if (input('_token') == Session::token()) {

            $user = Auth::getUser();

            if ($user->eth_address == post('eth_address')) {
                $rules['eth_address'] = 'min:42|max:42';
            } else {
                $rules['eth_address'] = 'min:42|max:42|unique:users';
            }

            $validator = Validator::make([
                'eth_address' => post('eth_address')
            ], $rules);

            if ($validator->fails()) {

                Flash::error($validator->messages()->first());

            } else {
                $user->update(['eth_address' => post('eth_address')]);
                Flash::success('You\'re ethereum wallet address has been updated');
            }
        }
    }

    public function onUpdateNickname()
    {

        Recaptcha::verifyCaptcha();

        if (input('_token') == Session::token()) {

            $user = Auth::getUser();

            $rules['nickname'] = 'required|min:0|max:160';

            $validator = Validator::make([
                'nickname' => post('nickname')
            ], $rules);

            if ($validator->fails()) {
                Flash::error($validator->messages()->first());
            } else {
                $user->update(['nickname' => post('nickname')]);
                Flash::success('You\'re nickname has been updated');
            }
        }
    }

    public function onUpdateSocialNetworks()
    {

        Recaptcha::verifyCaptcha();
        if (input('_token') == Session::token()) {

            $user = Auth::getUser();
            foreach (post() as $key => $value) {
                if ($key != 'g-recaptcha-response' && $key != '_session_key' && $key != '_token') {
                    $rules[$key] = 'min:0|max:255';
                }
            }

            $validator = Validator::make(post(), $rules);

            if ($validator->fails()) {
                Flash::error($validator->messages()->first());

            } else {

                $user->update([
                    'telegram_username'  => post('telegram_username'),
                    'facebook_link'      => post('facebook_link'),
                    'youtube_link'       => post('youtube_link'),
                    'twitter_link'       => post('twitter_link'),
                    'btc_link'           => post('btc_link'),
                ]);
                
                Flash::success('You\'re profile has been updated');
            }
        }
    }
}