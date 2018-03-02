<?php namespace CryptoPolice\Bounty\Components;

use Cms\Classes\ComponentBase;
use CryptoPolice\Bounty\Models\BountyReport;

class ReportDetails extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Report details',
            'description' => 'Report Description'
        ];
    }

    public function onRun()
    {
        $this->page['report'] = BountyReport::with('user')->select('cryptopolice_bounty_user_reports.id as report_id','cryptopolice_bounty_user_reports.*','users.*')
            ->where('cryptopolice_bounty_user_reports.id', $this->param('id'))
            ->join('users', 'users.id', '=', 'cryptopolice_bounty_user_reports.user_id')
            ->first();
    }
}