<?php namespace CryptoPolice\FraudVerification\Components;

use Cms\Classes\ComponentBase;
use CryptoPolice\FraudVerification\Models\BecomeToOfficer as BecomeToOfficer;
use CryptoPolice\Academy\Components\Recaptcha as Recaptcha;
use Auth, Flash,Input,Session,Validator,ValidationException, Redirect;


/**
 * Model
 */
class Officer extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Become To Officer',
            'description' => 'Become To CryptoPolice Officer'
        ];
    }

    public function onRun()
    {
        //$this->page['bountyList'] = ::orderBy('sort_order', 'asc')->get();

       $this->page['isUserOfficer'] = $this->getIsUserOfficer(true);
       $this->page['isUserSendApplicationToBecomeOfficer'] = $this->getIsUserOfficer(false);



    }

    public function getIsUserOfficer($status){

        $user = Auth::getUser();

        $isUserOfficer = BecomeToOfficer::where('user_id', $user->id)
            ->where('status', $status)
            ->count();

        return $isUserOfficer;
    }

    public function onBecomeToOfficer()
    {

        Recaptcha::verifyCaptcha();

        if (input('_token') == Session::token()) {

            $user = Auth::getUser();


            // Personal data
            $name           =   strip_tags(trim(post('name')));
            $surname        =   strip_tags(trim(post('surname')));
            $ethAddress     =   strip_tags(trim(post('eth_address')));
            $email          =   strip_tags(trim(post('email')));
            $nickname       =   strip_tags(preg_replace('/\s+/', '', post('nickname')));
            $country_id     =   post('country_id');
            $company        =   strip_tags(trim(post('company')));


            // Rulles
            $validator = Validator::make(
                [
                    'eth_address' => $ethAddress,
                    'email' => $email,
                    'nickname' => $nickname,
                    'name' => $name,
                    'surname' => $surname,
                    'country_id' => $country_id,
                ],
                [
                    'eth_address' => 'required|min:42|max:42',
                    'email' => 'required',
                    'nickname' => 'required|min:3|max:160'
                ]
            );


            if ($validator->fails()) {
                Flash::error($validator->messages()->first());
            } else {

                $user->update([
                    'name'          => $name,
                    'surname'       => $surname,
                    'eth_address'   => $ethAddress,
                    'email'         => $email,
                    'nickname'      => $nickname,
                    'country_id'    => $country_id,
                    'company'       => $company,

                ]);

                // Submitting application
                $newOfficerSubmittion = new BecomeToOfficer;
                $newOfficerSubmittion->user_id = $user->id;
                $newOfficerSubmittion->save();

                Flash::success('You\'re request has been successfully submitted! Wait for the answer! ');

                return Redirect::to('dashboard');


            }







        }
    }

}
