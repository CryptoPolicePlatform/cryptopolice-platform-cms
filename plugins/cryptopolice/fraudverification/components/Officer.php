<?php namespace CryptoPolice\FraudVerification\Components;

use Cms\Classes\ComponentBase;
use CryptoPolice\FraudVerification\Models\BecomeToOfficer as BecomeToOfficer;
use CryptoPolice\FraudVerification\Models\Application as FraudApplications;
use CryptoPolice\FraudVerification\Models\ApplicationVerdicts as VerdictTypes;
use CryptoPolice\FraudVerification\Models\Verdict as Verdict;
use CryptoPolice\FraudVerification\Models\VerficationLevels as Levels;
use CryptoPolice\FraudVerification\Models\VerificationsUsers as VerificationsUsers;
use CryptoPolice\FraudVerification\Models\ApplicationTypes as ApplicationTypes;
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



        if ($this->page->url == "/fraud-application/:id") {
            // Fraud application page
            $this->page['FraudApplications'] = $this->getFraudApplications($this->param('id'));
            $this->page['VerdictTypes'] = $this->getVerdictTypes();
            $this->page['Verdicts'] = $this->getVerdicts($this->param('id'), false);
            $this->page['CanUserVerifyThisApplication'] = $this->getIsUserAbleToVerifyApplication($this->param('id'), $user->id);

        }elseif($this->page->url == "/verdict/:id/:app_id") {
            // Verdict page
            $this->page['Verdict'] = $this->getVerdicts($this->param('app_id'),false,$this->param('id'));
            $this->page['CanUserVerifyThisVerdict'] = $this->getIsUserAbleToVerifyVerdict($this->param('id'), $user->id,$this->param('app_id'));
            $this->page['VerdictTypes'] = $this->getVerdictTypes();
            $this->page['VerificationLevel'] = $this->getVerificationLevel( $user->id,$this->param('id'), $user->id,$this->param('app_id'));

        }else{
           // Dashboard page
           $this->page['WaitForVerification'] = $this->getFraudApplicationsToVerification($user->id);
           $this->page['WaitForVerificationVerdicts'] = $this->getVerdictsToVerification($user->id);
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

        return true;
    }

    public function getFraudApplicationsToVerification($user_id){

        return Db::table('cryptopolice_fraudverification_verification_users')
            ->LeftJoin('cryptopolice_fraudverification_application', 'cryptopolice_fraudverification_application.id', '=', 'cryptopolice_fraudverification_verification_users.application_id')
            ->LeftJoin('cryptopolice_fraudverification_application_types', 'cryptopolice_fraudverification_application_types.id', '=', 'cryptopolice_fraudverification_application.type_id')
            ->LeftJoin('cryptopolice_fraudverification_verdict', 'cryptopolice_fraudverification_verdict.application_id', '=', 'cryptopolice_fraudverification_application.id')
            ->select('cryptopolice_fraudverification_application.*', 'cryptopolice_fraudverification_verification_users.id as verify_id','cryptopolice_fraudverification_application_types.type as type_title')
            ->where('cryptopolice_fraudverification_verification_users.user_id',$user_id)
            ->whereNull('cryptopolice_fraudverification_verdict.id')
            ->whereNull('cryptopolice_fraudverification_application.deleted_at')
            ->where('cryptopolice_fraudverification_application.status',true)
            ->get();

    }

    public function getVerdictsToVerification($user_id){

      /*  return Db::table('cryptopolice_fraudverification_verification_users')
            ->join('cryptopolice_fraudverification_verdict', 'cryptopolice_fraudverification_verdict.id', '=', 'cryptopolice_fraudverification_verification_users.verdict_id')
            ->join('cryptopolice_fraudverification_application_verdicts', 'cryptopolice_fraudverification_application_verdicts.id', '=', 'cryptopolice_fraudverification_verdict.verdict_type_id')
            ->join('cryptopolice_fraudverification_verification_levels', 'cryptopolice_fraudverification_verification_levels.id', '=', 'cryptopolice_fraudverification_verdict.verification_id')
            ->select('cryptopolice_fraudverification_verdict.*', 'cryptopolice_fraudverification_verification_users.id as verify_id','cryptopolice_fraudverification_application_verdicts.verdict as type_title','cryptopolice_fraudverification_verification_levels.level as level_title')
            ->where('cryptopolice_fraudverification_verification_users.user_id',$user_id)
            //->where('cryptopolice_fraudverification_verdict.id','!=','cryptopolice_fraudverification_verdict.id')
            ->where('cryptopolice_fraudverification_verdict.status',true)
            ->whereNull('cryptopolice_fraudverification_verdict.deleted_at')
            //->whereNull('cryptopolice_fraudverification_verdict.parent_id')
            ->get();*/

        return VerificationsUsers::where('status',true)->where('user_id',$user_id)->where('application_id','')->get();

    }

    public function getIsUserOfficer($status){

        $user = Auth::getUser();

        $isUserOfficer = BecomeToOfficer::where('user_id', $user->id)
            ->where('status', $status)
            ->count();

        return $isUserOfficer;
    }

    public function onSubmitVerdict($verification_id = 1, $parent_id = null)
    {
        Recaptcha::verifyCaptcha();

        if (input('_token') == Session::token()) {

            $user = Auth::getUser();

            // Data

            $verdict_type_id =  intval(trim(post('verdict_type_id')));
            $comment    =  strip_tags(trim(post('comment')));

            if ($this->page->url == "/fraud-application/:id") {

                $application_id = strip_tags(trim($this->param('id')));

            }elseif($this->page->url == "/verdict/:id/:app_id") {

                $application_id = intval(trim($this->param('app_id')));
                $verdict_id = intval(trim($this->param('id')));
                $parent_id = intval(trim(post('parent_id')));

                // get  verdict level
                $level =  VerificationsUsers::where('user_id',$user->id)->where('verdict_id',$verdict_id)->where('application_id',$application_id)->first();
                $verification_id = $level->level->id;
            }



            // Rulles
            $validator = Validator::make(
                [
                    'verdict_id' => $verdict_type_id,
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
                $newVerdict->verdict_type_id    = $verdict_type_id;
                $newVerdict->comment            = $comment;
                $newVerdict->application_id     = $application_id;
                $newVerdict->verification_id    = $verification_id;
                $newVerdict->parent_id          = $parent_id;

                $newVerdict->save();

                // Send to verification
                $this->SendToVerification($newVerdict->user_id, $newVerdict->application_id, $newVerdict->id,2);

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

    public function getVerdicts($app_id = false, $user_id = false, $verdict_id = false)
    {
        if ( $verdict_id && $app_id && $user_id == false ){
           return  Verdict::where('status', true)->where('id',$verdict_id)->where('application_id',$app_id)->where('status', true)->first();
        }
        if ( $app_id && $user_id == false && $verdict_id == false ){
            return Verdict::where('status', true)->where('application_id',$app_id)->orderBy('id','asc')->get();
        }

        if( $user_id && $app_id == false && $verdict_id == false){
            return Verdict::where('status', true)->where('user_id',$user_id)->where('status', true)->orderBy('id','asc')->get();
        }

        if( $user_id && $app_id && $verdict_id == false  ){
            return Verdict::where('status', true)->where('user_id',$user_id)->where('application_id',$app_id)->where('status', true)->orderBy('id','asc')->get();
        }

        return false;

    }

    public function getIsUserAbleToVerifyApplication($app_id,$user_id){

        $IsOfficerNominatedForThisApplication =   VerificationsUsers::where('user_id',$user_id)->where('application_id',$app_id)->count();
        $IsOfficerSubmittedVerdictForThisApplication =   Verdict::where('user_id',$user_id)->where('application_id',$app_id)->count();

        if($IsOfficerNominatedForThisApplication == 1 &&  $IsOfficerSubmittedVerdictForThisApplication == 0) {
            return true;
        }else{
            return false;
        }

    }


    public function getIsUserAbleToVerifyVerdict($verdict_id,$user_id,$app_id){


        $IsOfficerNominatedForThisVerdict =   VerificationsUsers::where('user_id',$user_id)->where('verdict_id',$verdict_id)->where('application_id',$app_id)->count();
        $IsOfficerAlreadySubmittedVerdict =   Verdict::where('user_id',$user_id)->where('application_id',$app_id)->count();



        if($IsOfficerNominatedForThisVerdict == 1 &&  $IsOfficerAlreadySubmittedVerdict == 0) {
            return true;
        }else{
            return false;
        }

    }

    public function getVerificationLevel($user_id,$verdict_id,$app_id){

      VerificationsUsers::where('user_id',$user_id)->where('verdict_id',$verdict_id)->where('application_id',$app_id)->first();
        return true;

    }


    public static function onSubmitFraudApplication($user = 2, $domain, $task, $application_type_id){

        $user = intval(trim($user));
        $domain = strip_tags(trim($domain));
        $task = strip_tags(trim($task));
        $application_type_id = intval(trim($application_type_id));

        // Rulles
        $validator = Validator::make(
            [
                'user_id' => $user,
                'domain' => $domain,
                'task' => $task,
                'application_type' => $application_type_id
            ],
            [
                'user_id' => 'required|min:1|numeric',
                'domain' => 'required|min:4|max:255',
                'task' => 'required|min:3|max:5000',
                'application_type' => 'required|min:1|numeric'
            ]
        );


        if ($validator->fails()) {
            return error($validator->messages()->first());
        } else {


            // Submitting application
            $new = new FraudApplications;
            $new->user_id = $user;
            $new->domain = $domain;
            $new->task = $task;
            $new->type_id = $application_type_id;
            $new->status = 0;

            $new->save();

            return "Your report is successfully submitted for verification";
        }



    }


    public static function GetApplicationTypes(){

        return ApplicationTypes::where('status', true)->get();
    }

}
