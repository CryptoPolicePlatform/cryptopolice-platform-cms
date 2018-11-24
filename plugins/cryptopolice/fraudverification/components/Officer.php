<?php namespace CryptoPolice\FraudVerification\Components;

use Cms\Classes\ComponentBase;
use CryptoPolice\FraudVerification\Models\BecomeToOfficer as BecomeToOfficer;
use CryptoPolice\FraudVerification\Models\Application as FraudApplications;
use CryptoPolice\FraudVerification\Models\ApplicationVerdicts as VerdictTypes;
use CryptoPolice\FraudVerification\Models\Verdict as Verdict;
use CryptoPolice\FraudVerification\Models\VerficationLevels as Levels;
use CryptoPolice\FraudVerification\Models\VerificationsUsers as VerificationsUsers;
// use Rainlab\User\Models\User as Users;
use CryptoPolice\Academy\Components\Recaptcha as Recaptcha;
use Auth, Db, Flash,Input,Session,Validator,ValidationException, Redirect, Log;


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
        $user = Auth::getUser();
        $this->page['isUserOfficer'] = $this->getIsUserOfficer(true);

       if($this->param('id')){

           // Fraud application page
           $this->page['FraudApplications'] = $this->getFraudApplications($this->param('id'));
           $this->page['VerdictTypes'] = $this->getVerdictTypes();
           $this->page['Verdicts'] = $this->getVerdicts($this->param('id'),false);
           $this->page['CanUserVerifyThisApplication'] = $this->getIsUserAbleToVerifyApplication($this->param('id'),$user->id);

       }else{

           // Dashboard
           $this->page['WaitForVerification'] = $this->getFraudApplicationsToVerification($user->id);
           $this->page['FraudApplications'] = $this->getFraudApplications();
           $this->page['MyApplications'] = $this->getFraudApplications(false,$user->id);
           $this->page['isUserSendApplicationToBecomeOfficer'] = $this->getIsUserOfficer(false);
           $this->page['MyVerdicts'] = $this->getVerdicts(false, $user->id);
       }




    }

    public static function SendToVerification($UserId, $ApplicationId, $VerdictId = null, $level = 1){


        // Get verification level data
        if($level){
            // Get level data
            $LevelData = Levels::where('status', true)->where('verification_order', $level)->first();
            // Get level Officer amount
            $LevelOfficer_amount = $LevelData->officer_count;
        }


        // Select random officers
        $OfficersToVerification = BecomeToOfficer::where('status', true)->where('user_id', '!=' , $UserId )->inRandomOrder()->take($LevelOfficer_amount)->get();

        // Save officers to verification
        foreach($OfficersToVerification as $officer ){

           $SaveOfficerToVerification =  new VerificationsUsers();
           $SaveOfficerToVerification->user_id = $officer->user_id;
           $SaveOfficerToVerification->application_id = $ApplicationId;
           $SaveOfficerToVerification->verdict_id = $VerdictId;
           $SaveOfficerToVerification->level_id = $LevelData->id;
           $SaveOfficerToVerification->status = true;
           $SaveOfficerToVerification->type = 1;
           $SaveOfficerToVerification->save();

        }


        return $OfficersToVerification;
    }

    public function getFraudApplicationsToVerification($user_id){




        return   Db::table('cryptopolice_fraudverification_application')
            ->join('cryptopolice_fraudverification_verification_users', 'cryptopolice_fraudverification_verification_users.application_id', '=', 'cryptopolice_fraudverification_application.id')
            ->join('cryptopolice_fraudverification_application_types', 'cryptopolice_fraudverification_application_types.id', '=', 'cryptopolice_fraudverification_application.type_id')
            ->select('cryptopolice_fraudverification_application.*', 'cryptopolice_fraudverification_verification_users.id as verify_id','cryptopolice_fraudverification_application_types.type as type_title')
            ->where('cryptopolice_fraudverification_verification_users.user_id',$user_id)
            ->where('cryptopolice_fraudverification_application.status',true)
            ->groupBy('user_id')
            ->get();



    }

    public function getIsUserOfficer($status){

        $user = Auth::getUser();

        $isUserOfficer = BecomeToOfficer::where('user_id', $user->id)
            ->where('status', $status)
            ->count();

        return $isUserOfficer;
    }

    public function onSubmitVerdict()
    {
        Recaptcha::verifyCaptcha();

        if (input('_token') == Session::token()) {

            $user = Auth::getUser();

            // Data

            $verdict_id =  strip_tags(trim(post('verdict_id')));
            $comment    =  strip_tags(trim(post('comment')));
            $application_id = strip_tags(trim($this->param('id')));

            // Rulles

            $validator = Validator::make(
                [
                    'verdict_id' => $verdict_id,
                    'comment' => $comment,
                    'application_id' => $application_id
                ],
                [
                    'verdict_id' => 'required|numeric|min:1',
                    'comment' => 'required|min:5|max:5000',
                    'application_id' => 'required|numeric|min:1'
                ]
            );

            if ($validator->fails()) {
                Flash::error($validator->messages()->first());
            } else {

                // Submitting application
                $newVerdict = new Verdict;
                $newVerdict->user_id            = $user->id;
                $newVerdict->verdict_type_id    = $verdict_id;
                $newVerdict->comment            = $comment;
                $newVerdict->application_id     = $application_id;
                $newVerdict->verification_id    = 1;

                $newVerdict->save();

                Flash::success('You\'re verdict has been successfully submitted! ');

                return Redirect::to('dashboard');
            }

        }
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

    public function getFraudApplications($id = false, $user = false)
    {
        if($id){
            $FraudApplications = FraudApplications::where('id', $id)->where('status', true)->first();
        }elseif($user) {
            $FraudApplications = FraudApplications::where('user_id', $user)->orderBy('id','desc')->where('status', true)->get();
        }else{
            $FraudApplications = FraudApplications::where('status', true)->orderBy('id','desc')->get();
        }

        return $FraudApplications;
    }

    public function getVerdictTypes()
    {

        $VerdictTypes = VerdictTypes::where('status', true)->orderBy('order','asc')->get();

        return $VerdictTypes;
    }

    public function getVerdicts($app_id = false, $user_id = false)
    {
        if ( $app_id && $user_id == false ){
            $Verdicts = Verdict::where('status', true)->where('application_id',$app_id)->where('status', true)->orderBy('id','asc')->get();
        }

        if( $user_id && $app_id == false ){
            $Verdicts = Verdict::where('status', true)->where('user_id',$user_id)->where('status', true)->orderBy('id','asc')->get();
        }

        if( $user_id && $app_id  ){
            $Verdicts = Verdict::where('status', true)->where('user_id',$user_id)->where('application_id',$app_id)->where('status', true)->orderBy('id','asc')->get();
        }


        return $Verdicts;
    }

    public function getIsUserAbleToVerifyApplication($app_id,$user_id){

        return  VerificationsUsers::where('user_id',$user_id)->where('application_id',$app_id)->count();

    }

}
