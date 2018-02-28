<?php namespace CryptoPolice\Bitcointalk;

use Backend, Event;
use System\Classes\PluginBase;

/**
 * Bitcointalk Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Bitcointalk',
            'description' => 'No description provided yet...',
            'author'      => 'CryptoPolice',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        Event::listen('bitcointalk.contentSaved', 'CryptoPolice\Bitcointalk\Classes\EventListeners\BtcAccountVerification');
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

        return [
            'CryptoPolice\Bitcointalk\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'cryptopolice.bitcointalk.some_permission' => [
                'tab' => 'Bitcointalk',
                'label' => 'Some permission'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return [
            'bitcointalk' => [
                'label'       => 'Bitcointalk',
                'url'         => Backend::url('cryptopolice/bitcointalk/topic'),
                'icon'        => 'icon-leaf',
                'iconSvg'     => 'plugins/cryptopolice/bitcointalk/assets/image/logo.svg',
                'permissions' => ['cryptopolice.bitcointalk.*'],
                'order'       => 500,
                'sideMenu' => [
                    'talk' => [
                        'label'       => 'Topic',
                        'url'         => Backend::url('cryptopolice/bitcointalk/topic'),
                        'icon'        => 'icon-rss',
                    ],
                    'pages' => [
                        'label'       => 'Pages',
                        'url'         => Backend::url('cryptopolice/bitcointalk/page'),
                        'icon'        => 'icon-rss-square',
                    ],
                    'contents' => [
                        'label'       => 'Contents',
                        'url'         => Backend::url('cryptopolice/bitcointalk/content'),
                        'icon'        => 'icon-wifi',
                    ],
                ]
            ],
        ];
    }
}
