<?php namespace CryptoPolice\Platform\Components;

use Cms\Classes\ComponentBase;

use CryptoPolice\Academy\Models\Exam;
use CryptoPolice\Academy\Models\Training;
use CryptoPolice\Academy\Models\FinalScore;
use CryptoPolice\Academy\Models\TrainingView;
use CryptoPolice\Academy\Components\Recaptcha as Recaptcha;

use CryptoPolice\Bounty\Models\BountyReport;
use CryptoPolice\Bounty\Models\BountyRegistration;

use CryptoPolice\Platform\Models\CommunityPost;
use CryptoPolice\Platform\Models\CommunityComment;

use Auth, Flash,Input,Session,Validator,ValidationException;
use October\Rain\Auth\Manager;

class Profile extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'          => 'Dashboard',
            'description'   => 'Users dashboard'
        ];
    }

    public function onRun()
    {

        // Community Statistic
        $this->page['posts_count']              = $this->getUsersPostsCount();
        $this->page['comments_count']           = $this->getUsersCommentsCount();
        $this->page['activity']                 = $this->getUsersActivity($this->page['posts_count'], $this->page['comments_count']);
        
        // Bounty Campaign Statistic
        $this->page['bounty_reports']           = $this->getUsersReports();
        $this->page['bounty_registrations']     = $this->getUsersBountyRegistrations();
       
        // Academy Statistic
        $this->page['training_count']           = $this->getTrainingCount();
        $this->page['training_viewed']          = $this->getUsersViewedTrainings();
        $this->page['exam_scores']              = $this->getExamScores();
    }

    public function getPercentageDifference($amount, $first, $second) {
        if ($amount - $first && $second) {
            return (100 / $amount * $second) / 100;
        } else {
            return 0;
        }
    }

    public function getUsersActivity($postsNum, $commentsNum) {
        return ($postsNum * 5 + $commentsNum) / 100;
    }

    public function getUsersReports() {

        $usersReportsList = BountyReport::with('reward', 'bounty')
            ->where('user_id', Auth::getUser()->id)
            ->get();

        $numOfReports        = $usersReportsList->count();
        $pendingReports      = $usersReportsList->where('report_status', 0)->count();
        $approvedReports     = $usersReportsList->where('report_status', 1)->count();

        $this->page['bounty_report_percentage']     = $this->getPercentageDifference($numOfReports, $pendingReports, $approvedReports);
        $this->page['bounty_report_count']          = $numOfReports;   
        $this->page['bounty_report_approved']       = $approvedReports;
        $this->page['bounty_report_pending']        = $pendingReports;
        $this->page['bounty_report_disapproved']    = $usersReportsList->where('report_status', 2)->count();

        return $usersReportsList;
    }

    public function getUsersBountyRegistrations() {

        $usersRegistrationsList = BountyRegistration::with('bounty', 'bountyReport.reward')
            ->where('user_id', Auth::getUser()->id)
            ->get();

        // Get total amount of stakes and tokens for each registered campaign
        foreach ($usersRegistrationsList as $key => $value) {
           // Summarize, the stakes and tokens earned in each campaign
           $usersRegistrationsList[$key]['given_reward'] = $value->bountyReport->sum('given_reward');
            
            // Check if report is defined 
            if (isset($value->bountyReport[0])) {
                // Get reward type in currnet bounty campaign
                $usersRegistrationsList[$key]['reward_type'] = $value->bountyReport[0]->reward->reward_type;
            } else {
                $usersRegistrationsList[$key]['reward_type'] = null;
            }
        }

        $numOfRegistrations       = $usersRegistrationsList->count();
        $pendingRegistrations     = $usersRegistrationsList->where('approval_type', 0)->count();
        $approvedRegistrations    = $usersRegistrationsList->where('approval_type', 1)->count();
        $blockedRegistrations     = $usersRegistrationsList->where('status', 0)->count();

        $this->page['bounty_registrations_percentage']  = $this->getPercentageDifference($numOfRegistrations, $pendingRegistrations, $approvedRegistrations);
        $this->page['bounty_registrations_count']       = $numOfRegistrations;
        $this->page['bounty_registrations_pending']     = $pendingRegistrations;
        $this->page['bounty_registrations_approved']    = $approvedRegistrations;
        $this->page['bounty_registrations_blocked']     = $usersRegistrationsList->where('status', 0)->count();

        return $usersRegistrationsList;
    }

    public function getUsersPostsCount() {
        return CommunityPost::where('user_id', Auth::getUser()->id)
            ->count();
    }

    public function getUsersCommentsCount() {
        return CommunityComment::where('user_id', Auth::getUser()->id)
            ->count();
    }
    public function getExamCount() {
        return Exam::count();
    }

    public function getExamScores()
    {

        $exams = FinalScore::with('exam')
            ->where('user_id', Auth::getUser()->id)
            ->get();

        $this->page['exam_list'] = $this->getUniqueExamList($exams);

        return $exams;
    }

    public function getUniqueExamList($exams)
    {

        $buf = [];
        $uniqueExamList = [];

        foreach ($exams as $exam) {
            if(isset($exam->exam_id)) {
                array_push($buf, $exam->exam_id);
            }
        }

        foreach (array_unique($buf) as $key => $val) {
            if (isset($exams[$key]->exam->exam_title)) {
                array_push($uniqueExamList,  [
                    'id'    => $exams[$key]->exam->id,
                    'title' => $exams[$key]->exam->exam_title
                ]);
            }
        }

        return $uniqueExamList;
    }

    public function getTrainingCount() {
        return Training::count(); 
    }

    public function getUsersViewedTrainings() {
        return TrainingView::with('training')
            ->where('user_id', Auth::getUser()->id)
            ->count();
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

    public function verifyUser()
    {

        $user = Auth::getUser();

        $credentials = [
            'login'     => $user->email,
            'password'  => post('password')
        ];

        $userAccess = Auth::authenticate($credentials, true);
        if (isset($userAccess) && !empty($userAccess)) {

            return $userAccess->isBanned() ? Auth::logout() : true;

        } else {
            Flash::success('Your password is invalid. Please try again!');
            return false;
        }
    }

    public function onUpdateWalletAddress()
    {

        Recaptcha::verifyCaptcha();

        if (input('_token') == Session::token()) {

            if ($this->verifyUser()) {

                $user = Auth::getUser();
                $ethAddress = strip_tags(trim(post('eth_address')));

                $rules['eth_address'] = $user->eth_address == $ethAddress ? 'min:42|max:42' : 'min:42|max:42|unique:users';

                $validator = Validator::make(['eth_address' => $ethAddress], $rules);

                if ($validator->fails()) {

                    Flash::error($validator->messages()->first());

                } else {
                    $user->update(['eth_address' => $ethAddress]);
                    Flash::success('You\'re ethereum wallet address has been successfully changed');
                }
            }
        }
    }

    public function onEmailUpdate()
    {
        Recaptcha::verifyCaptcha();

        if (input('_token') == Session::token()) {

            if ($this->verifyUser()) {

                $user = Auth::getUser();
                $user->update(['email' => post('email')]);

                Flash::success('Your email address has been successfully changed');
            }
        }
    }


    public function onUpdateNickname()
    {

        Recaptcha::verifyCaptcha();

        if (input('_token') == Session::token()) {

            $user = Auth::getUser();

            $nickname = strip_tags(preg_replace('/\s+/', '', post('nickname')));

            $rules['nickname'] = 'required|min:0|max:160|unique:users';

            $validator = Validator::make([
                'nickname' => $nickname
            ], $rules);

            if ($validator->fails()) {
                Flash::error($validator->messages()->first());
            } else {
                $user->update(['nickname' => $nickname]);
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
                    'telegram_username' => post('telegram_username'),
                    'facebook_link'     => post('facebook_link'),
                    'youtube_link'      => post('youtube_link'),
                    'twitter_link'      => post('twitter_link'),
                    'btc_username'      => post('btc_username'),
                    'btc_link'          => post('btc_link'),
                ]);

                Flash::success('You\'re profile has been updated');
            }
        }
    }
}