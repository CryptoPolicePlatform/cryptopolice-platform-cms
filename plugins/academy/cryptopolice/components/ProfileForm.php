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
use Redirect;
use Validator;
use Cms\Classes\ComponentBase;
use RainLab\User\Models\User;

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
        $userID = $user->id;
        $rules = [
            'name' => 'required',
            'country_id' => 'required',
            'surname' => 'required|min:8',
            'email' => 'required|email|unique:users',
        ];

        $validator = Validator::make(post(), $rules);

        if (!$validator->fails()) {

            User::where('id', $userID)
                ->update([
                    'name' => post('name'),
                    'surname' => post('surname'),
                    'country_id' => post('country'),
                    'email' => post('email')
                ]);

            Flash::success('Your profile has been successfully updated');

        } else {

            $messages = $validator->messages();
            foreach ($messages->all() as $message) {
                Flash::error($message);
            }
        }
    }
}