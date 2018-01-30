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
            $this->reportList = $user->bountyReports()
                ->wherePivot('bounty_campaigns_id', post('campaign_type'))
                ->get();

        } elseif (post('status')) {

            $this->reportList = $user->bountyReports()
                ->wherePivot('status', post('campaign_type'))
                ->get();

        } else {
            $this->reportList = $user->userReportList()
                ->orderBy('created_at', 'desc')
                ->get();
        }
    }
}