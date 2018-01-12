<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 18.11.1
 * Time: 11:44
 */

namespace Academy\CryptoPolice\Components;

use Auth;
use Flash;
use Input;
use Redirect;
use Validator;
use ValidationException;
use Cms\Classes\ComponentBase;

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

        $user = Auth::getUser();

        if ($user) {

            $rules = [
                'eth_address' => 'min:42|max:42|unique:users',
            ];
            $validator = Validator::make(['eth_address' => post('eth_address')], $rules);

            if ($validator->fails()) {

                $messages = $validator->messages();
                foreach ($messages->all() as $message) {
                    Flash::error($message);
                }

            } else {
                $user->update(input());
                Flash::success('Profile has been successfully updated');
            }
        } else {
            return Redirect::to('/login');
        }
    }

}