<?php namespace CryptoPolice\Bounty;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
            'CryptoPolice\Bounty\Components\Bounties'         => 'Bounties',
            'CryptoPolice\Bounty\Components\UsersCampaign'    => 'UsersCampaign',
            'CryptoPolice\Bounty\Components\Report'           => 'Report',
        ];
    }

    public function registerSettings()
    {
    }
}
