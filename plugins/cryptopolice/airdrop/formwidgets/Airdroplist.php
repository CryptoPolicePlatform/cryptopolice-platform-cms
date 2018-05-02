<?php namespace Cryptopolice\Airdrop\FormWidgets;

use Config;
use Backend\Classes\FormWidgetBase;
use CryptoPolice\Bounty\Models\BountyReport;
use CryptoPolice\Academy\Models\Settings;

class Airdroplist extends FormWidgetBase
{
    public function widgetDetails()
    {
        return [
            'name' => 'Airdrop list',
            'description' => 'Airdrop list'
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

        return 'test';
    }
}