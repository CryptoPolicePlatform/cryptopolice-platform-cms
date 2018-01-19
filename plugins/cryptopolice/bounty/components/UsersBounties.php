<?php namespace CryptoPolice\Bounty\Components;

use Auth;
use Flash;
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
            ->select('cryptopolice_bounty_bounty_users.*', 'cryptopolice_bounty_bounty_campaigns.title as bounty_title')
            ->join('cryptopolice_bounty_bounty_campaigns', 'cryptopolice_bounty_bounty_users.bounty_campaigns_id', '=', 'cryptopolice_bounty_bounty_campaigns.id')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
