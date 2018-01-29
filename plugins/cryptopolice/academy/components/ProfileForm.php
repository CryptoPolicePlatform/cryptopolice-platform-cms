<?php namespace CryptoPolice\Academy\Components;

use Auth;
use Flash;
use Input;
use Redirect;
use Validator;
use ValidationException;
use Cms\Classes\ComponentBase;
use CryptoPolice\Academy\Components\Recaptcha as Recaptcha;

class ProfileForm extends ComponentBase
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
        $user = Auth::getUser();
        $user->update(post());
        Flash::success('Profile has been successfully updated');

    }

    public function onUpdateWalletAddress()
    {

        $user = Auth::getUser();

        if($user->eth_address == post('eth_address')) {
            $rules['eth_address'] = 'min:42|max:42';
        } else {
            $rules['eth_address'] = 'min:42|max:42|unique:users';
        }
        
        $validator = Validator::make([
            'eth_address' => post('eth_address')
        ], $rules);

        if ($validator->fails()) {

            $messages = $validator->messages();
            foreach ($messages->all() as $message) {
                Flash::error($message);
            }

        } else {
            $user->update(['eth_address' => post('eth_address')]);
            Flash::success('You\'re ethereum wallet address has been updated');
        }
    }

    public function onUpdateSocialNetworks() 
    {

        $user = Auth::getUser();
        foreach(post() as $key => $value ) {
            if($user[$key] == post($key)) { 
                $rules[$key] = 'min:0|max:255';
            } else {
                $rules[$key] = 'min:0|max:255|unique:users';
            }
        }
        
        $validator = Validator::make(post(), $rules);

        if ($validator->fails()) {
            $messages = $validator->messages();
            foreach ($messages->all() as $message) {
                Flash::error($message);
            }

        } else {
            $user->update(post());
            Flash::success('You\'re profile has been updated');
        }
    }
}