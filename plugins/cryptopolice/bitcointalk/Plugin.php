<?php namespace CryptoPolice\Bitcointalk;

use Backend;
use System\Classes\PluginBase;

use CryptoPolice\Bitcointalk\Models\Settings;
use CryptoPolice\Bitcointalk\Classes\CronJobs\Crawler;
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

    public function registerSettings()
    {
        return [
            'config' => [
                'label'       => 'Bitcointalk',
                'description' => 'Settings',
                'icon'        => 'icon-btc',
                'class'       => Settings::class,
                'order'       => 10,
                'permissions' => ['cryptopolice.bitcointalk.some_permission']
            ]
        ];
    }


    public function registerSchedule($schedule)
    {
        //*	*	*	*	* php /path/to/file/artisan schedule:run >> /dev/null 2>&1

        $schedule->call(function () {

            $settings = Settings::instance();

            if($settings->active) {

                if($settings->memory_usage_profiling) {

                    $mem_start = memory_get_usage();

                }

                $crawler = new Crawler();

                $crawler->run();


                if($settings->memory_usage_profiling) {

                    trace_log("Cron memory usage: " . (memory_get_usage() - $mem_start - sizeof($mem_start)) . " bytes");

                }
            }

        })->everyFiveMinutes()
            ->name('crawl')
            ->withoutOverlapping();
    }
}
