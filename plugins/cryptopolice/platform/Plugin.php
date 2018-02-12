<?php namespace CryptoPolice\Platform;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
            'CryptoPolice\Platform\Components\Posts'            => 'CommunityPosts',
            'CryptoPolice\Platform\Components\Users'            => 'CommunityUsers',
            'CryptoPolice\Platform\Components\PostDetails'      => 'CommunityPostDetails',
            'CryptoPolice\Platform\Components\PostComments'     => 'CommunityPostComments',
            'CryptoPolice\Platform\Components\Notifications'    => 'Notifications',
        ];
    }

    public function registerSettings()
    {
    }
}
