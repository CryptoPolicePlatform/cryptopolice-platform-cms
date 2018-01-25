<?php namespace CryptoPolice\Bounty\Components;

use DB;
use Auth;
use Flash;
use Redirect;
use Cms\Classes\ComponentBase;
use CryptoPolice\Bounty\Models\Bounty;

class Bounties extends ComponentBase
{

    public $bounty;

    public function componentDetails()
    {
        return [
            'name' => 'Bounty list',
            'description' => 'Bounty Campaign List'
        ];
    }

    public function onRun()
    {
        $this->bounty = Bounty::where('status', 1)->orderBy('sort_order', 'asc')->get();
    }

    public function onCheckRegistration()
    {

        $user = Auth::getUser();

        $access = $user->bountyCampaigns()->wherePivot('bounty_campaigns_id', post('id'))->first();

        if($access) {
            if($access->approval_type) {
                Flash::error('Your account is not approved yet');
            } else {
                return Redirect::to('bounty-campaign/'.$access->slug);
            }
        } else {
            Flash::error("You are not registered on this campaign");
        }
    }

}
