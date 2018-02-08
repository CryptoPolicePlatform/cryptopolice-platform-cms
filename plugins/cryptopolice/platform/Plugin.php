<?php namespace CryptoPolice\Platform;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
            'CryptoPolice\Platform\Components\Posts'         => 'CommunityPosts',
            'CryptoPolice\Platform\Components\Users'         => 'CommunityUsers',
        ];
    }

    public function registerSettings()
    {
    }
}
