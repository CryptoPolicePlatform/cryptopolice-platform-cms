<?php namespace CryptoPolice\Bounty;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
            'CryptoPolice\Bounty\Components\Bounties'         => 'Bounties',
            'CryptoPolice\Bounty\Components\UsersCampaign'    => 'UsersCampaign',
            'CryptoPolice\Bounty\Components\ReportDetails'    => 'ReportDetails'
        ];
    }

    public function registerSettings()
    {

    }

    public function registerFormWidgets()
    {
        return [
            'CryptoPolice\Bounty\FormWidgets\Registrationbox' => [
                'label' => 'Registration Box',
                'code'  => 'registrationbox'
            ]    
        ];
    }
}
