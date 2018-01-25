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

        $rules = [
            'eth_address' => 'min:42|max:42',
        ];

        $validator = Validator::make([
            'eth_address' => post('eth_address')], $rules);

        if ($validator->fails()) {

            $messages = $validator->messages();
            foreach ($messages->all() as $message) {
                Flash::error($message);
            }

        } else {
            if((string)input('eth_address') === (string)'') {
                $user->update(input(), ['eth_address' => null]);
            } else {
                $user->update(input());
            }
            Flash::success('Profile has been successfully updated');
        }
    }
}