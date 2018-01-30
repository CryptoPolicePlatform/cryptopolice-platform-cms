<?php namespace CryptoPolice\Bounty\Components;

use Auth;
use Cms\Classes\ComponentBase;
use CryptoPolice\Bounty\Models\BountyReport;

class Report extends ComponentBase
{

    public $reportList;

    public function componentDetails()
    {
        return [
            'name' => 'Report list',
            'description' => 'Campaign Report List'
        ];
    }

    public function onRun()
    {
        $this->onFilterReports();
    }

    public function onFilterReports()
    {

        // TODO: need refactoring

        $user = Auth::getUser();

        if (post('campaign_type') && !empty(post('campaign_type'))) {

            $this->reportList = BountyReport::select('cryptopolice_bounty_user_reports.*', 'cryptopolice_bounty_campaigns.title as bounty_title')
                ->join('cryptopolice_bounty_campaigns', 'cryptopolice_bounty_user_reports.bounty_campaigns_id', '=', 'cryptopolice_bounty_campaigns.id')
                ->where('cryptopolice_bounty_campaigns.id', post('campaign_type'))
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

        } elseif (post('status')) {

            $this->reportList = BountyReport::select('cryptopolice_bounty_user_reports.* as report', 'cryptopolice_bounty_campaigns.title as bounty_title')
                ->join('cryptopolice_bounty_campaigns', 'cryptopolice_bounty_user_reports.bounty_campaigns_id', '=', 'cryptopolice_bounty_campaigns.id')
                ->where('cryptopolice_bounty_user_reports.status', post('status'))
                ->where('user_id', $user->id)
                ->where('cryptopolice_bounty_user_reports.bounty_campaigns_id', $this->param('id'))
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $this->reportList = BountyReport::select('cryptopolice_bounty_user_reports.*', 'cryptopolice_bounty_campaigns.title as bounty_title')
                ->join('cryptopolice_bounty_campaigns', 'cryptopolice_bounty_user_reports.bounty_campaigns_id', '=', 'cryptopolice_bounty_campaigns.id')
                ->where('user_id', $user->id)
                ->where('cryptopolice_bounty_user_reports.bounty_campaigns_id', $this->param('id'))
                ->orderBy('created_at', 'desc')
                ->get();
        }
    }


}