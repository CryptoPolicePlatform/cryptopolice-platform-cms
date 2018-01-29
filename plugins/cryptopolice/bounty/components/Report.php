<?php namespace CryptoPolice\Bounty\Components;

use Cms\Classes\ComponentBase;
use CryptoPolice\Bounty\Models\BountyReport;

class Report extends ComponentBase
{

    public $report;

    public function componentDetails()
    {
        return [
            'name' => 'Bounty list',
            'description' => 'Bounty Campaign List'
        ];
    }

    public function onRun()
    {
        $this->report = BountyReport::where('cryptopolice_bounty_user_reports.id', $this->param('id'))
            ->join('users', 'users.id', '=', 'cryptopolice_bounty_user_reports.user_id')
            ->first();
    }

}