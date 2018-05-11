<?php namespace CryptoPolice\Bounty\Components;

use Auth;
use Cms\Classes\ComponentBase;
use CryptoPolice\Bounty\Models\BountyReport;
use Illuminate\Support\Facades\Redirect;

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
        $user = Auth::getUser();

        if(isset($user) & !empty($user)) {
            $report = BountyReport::with('user', 'reward')->select('cryptopolice_bounty_user_reports.id as report_id', 'cryptopolice_bounty_user_reports.*', 'users.*')
                ->join('users', 'users.id', '=', 'cryptopolice_bounty_user_reports.user_id')
                ->join('cryptopolice_bounty_campaigns', 'cryptopolice_bounty_campaigns.id', '=', 'cryptopolice_bounty_user_reports.bounty_campaigns_id')
                ->where('cryptopolice_bounty_user_reports.id', $this->param('id'))
                ->Where(function ($query) use ($user) {
                    if (!$user->is_superuser) {
                        $query->where('user_id', $user->id)
                            ->orWhere('cryptopolice_bounty_campaigns.id', 3)
                            ->where('cryptopolice_bounty_user_reports.report_status', 1);
                    }
                })
                ->first();
        }
        if (!empty($report)) {
            $this->page['report'] = $report;
        } else {
            return Redirect::to('/bounty-campaign');
        }
    }
}