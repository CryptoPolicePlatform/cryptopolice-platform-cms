<?php namespace CryptoPolice\Platform\Components;

use Auth;
use CryptoPolice\Bounty\Models\BountyRegistration;
use CryptoPolice\Bounty\Models\BountyReport;
use CryptoPolice\Platform\Models\CommunityComment;
use CryptoPolice\Platform\Models\CommunityPost;
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

    public function onRun() {
        $this->page['bounty_reports'] = $this->getReportsStat();
        $this->page['bounty_registrations'] = $this->getBountyRegistrationsStat();
        $this->page['posts'] = $this->getPostsStat();
        $this->page['posts_count']  = $this->getPostsStat();
        $this->page['comments_count'] = $this->getCommentsStat();


        $this->page['activity'] = ($this->page['posts_count'] * 5 + $this->page['comments_count']) / 100;

    }

    public function getReportsStat()
    {

        $user = Auth::getUser();
        $reportList = BountyReport::with('reward', 'bounty')
            ->where('user_id', $user->id)
            ->get();

        $this->page['bounty_report_count'] =  $reportList->count();
        $this->page['bounty_report_disapproved'] = $reportList->where('report_status', 2)->count();
        $this->page['bounty_report_approved'] = $reportList->where('report_status', 1)->count();
        $this->page['bounty_report_pending'] = $reportList->where('report_status', 0)->count();

        return $reportList;
    }

    public function getBountyRegistrationsStat() {

        $user = Auth::getUser();
        $bountyRegistrationList = BountyRegistration::with('bounty')
            ->where('user_id', $user->id)
            ->get();

        $this->page['bounty_registrations_count'] = $bountyRegistrationList->count();
        $this->page['bounty_registrations_pending'] = $bountyRegistrationList->where('approval_type', 0)->count();
        $this->page['bounty_registrations_approved'] = $bountyRegistrationList->where('approval_type', 1)->count();
        $this->page['bounty_registrations_blocked'] = $bountyRegistrationList->where('status', 0)->count();

        return $bountyRegistrationList;
    }

    public function getPostsStat()
    {
        $user = Auth::getUser();
        return CommunityPost::where('user_id', $user->id)->count();
    }

    public function getCommentsStat()
    {
        $user = Auth::getUser();
        return CommunityComment::where('user_id', $user->id)->count();
    }


    public function getExamsStat() {

    }

    public function getTrainingStat() {

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