<?php namespace CryptoPolice\Bounty\Components;

use Auth;
use Flash;
use Validator;
use Cms\Classes\ComponentBase;
use CryptoPolice\Bounty\Models\BountyUser;

class UsersBounties extends ComponentBase
{
    public $usersBounties;
    public $historyBounty;

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

        $this->usersBounties = BountyUser::where('user_id', $user->id)
            ->select('cryptopolice_bounty_user_reports.*', 'cryptopolice_bounty_campaigns.title as bounty_title')
            ->join('cryptopolice_bounty_campaigns', 'cryptopolice_bounty_user_reports.bounty_campaigns_id', '=', 'cryptopolice_bounty_campaigns.id')
            ->orderBy('created_at', 'desc')
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

            BountyUser::insert([
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
