<?php namespace CryptoPolice\Bounty\Components;

use Cms\Classes\ComponentBase;
use CryptoPolice\Bounty\Models\BountyReport;

class ReportDetails extends ComponentBase
{

    public $report;

    public function componentDetails()
    {
        return [
            'name' => 'Report details',
            'description' => 'Report Description'
        ];
    }

    public function onRun()
    {
        $this->report = BountyReport::where('cryptopolice_bounty_user_reports.id', $this->param('id'))
            ->join('users', 'users.id', '=', 'cryptopolice_bounty_user_reports.user_id')
            ->first();
    }
}