<?php namespace CryptoPolice\Bounty;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
            'CryptoPolice\Bounty\Components\Report'           => 'Report',
            'CryptoPolice\Bounty\Components\Bounties'         => 'Bounties',
            'CryptoPolice\Bounty\Components\UsersCampaign'    => 'UsersCampaign',
        ];
    }

    public function registerSettings()
    {
    }
}
