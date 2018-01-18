<?php namespace CryptoPolice\CryptoPolice\Components;

use Auth;
use Flash;
use Cms\Classes\ComponentBase;
use CryptoPolice\CryptoPolice\Models\BountyUser;

class UsersBounties extends ComponentBase
{
    public $usersBounties;

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
            ->join('cryptopolice_cryptopolice_bounty_campaigns', 'cryptopolice_cryptopolice_bounty_users.bounty_campaigns_id', '=', 'cryptopolice_cryptopolice_bounty_campaigns.id')
            ->select('cryptopolice_cryptopolice_bounty_users.*', 'cryptopolice_cryptopolice_bounty_campaigns.title as bounty_title')
            ->get();
    }

}
