<?php namespace CryptoPolice\Bounty\Components;

use DB;
use Auth;
use Flash;
use Validator;
use Cms\Classes\ComponentBase;
use CryptoPolice\Bounty\Models\BountyReports;

class UsersReports extends ComponentBase
{
    public $userAccess;
    public $userReports;


    public function componentDetails()
    {
        return [
            'name' => 'Users Bounties',
            'description' => 'Users Bounties List'
        ];
    }
    public function onRun()
    {
        $user = Auth::getUser();

        $this->userReports = BountyReports::select('cryptopolice_bounty_user_reports.*', 'cryptopolice_bounty_campaigns.title as bounty_title')
            ->join('cryptopolice_bounty_campaigns', 'cryptopolice_bounty_user_reports.bounty_campaigns_id', '=', 'cryptopolice_bounty_campaigns.id')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $this->userAccess = DB::table('cryptopolice_bounty_user_registration')
            ->where('user_id', '=', $user->id)
            ->get();

    }

    public function onAddMessage() {

        $user = Auth::getUser();

        $rules = [
            'title' => 'required',
            'description' => 'required',
        ];

        $validator = Validator::make(post(), $rules);

        if ($validator->fails()) {

            $messages = $validator->messages();
            foreach ($messages->all() as $message) {
                Flash::error($message);
            }

        } else {

            BountyReports::insert([
                    'description' => post('description'),
                    'bounty_campaigns_id' => post('id'),
                    'title' => post('title'),
                    'user_id' => $user->id,
                    'status' => 0,
                ]
            );

            Flash::success('Report successfully sent');
        }
    }
}
