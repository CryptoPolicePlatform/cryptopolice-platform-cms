<?php namespace Cryptopolice\Bounty\FormWidgets;

use Config;
use Backend\Classes\FormWidgetBase;
use CryptoPolice\Bounty\Models\BountyReport;
use CryptoPolice\Academy\Models\Settings;

class ReportList extends FormWidgetBase
{
    public function widgetDetails()
    {
        return [
            'name' => 'Report list',
            'description' => 'List of users reports'
        ];
    }

    public function render()
    {
        $this->addCss('/modules/backend/formwidgets/repeater/assets/css/repeater.css');

        $this->prepareVars();
        return $this->makePartial('widget');
    }

    public function prepareVars()
    {

        $settings = Settings::instance();

        $this->vars['report_id'] = $this->model->id;

        if ($settings->campaign_reports_group) {
            $this->vars['reports_data'] = BountyReport::with('user', 'bounty')
                ->where([
                        ['bounty_user_registration_id', '=', $this->model->bounty_user_registration_id],
                        ['bounty_campaigns_id', '=', $this->model->bounty_campaigns_id]
                    ]
                )
                ->whereBetween('created_at', [$settings->campaign_reports_start_date, $settings->campaign_reports_end_date])
                ->get();
        } else {
            $this->vars['reports_data'] = BountyReport::with('user', 'bounty')
                ->where('id', $this->model->id)
                ->get();
        }
    }
}