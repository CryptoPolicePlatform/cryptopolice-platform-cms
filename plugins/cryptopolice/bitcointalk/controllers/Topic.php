<?php namespace CryptoPolice\Bitcointalk\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

use CryptoPolice\Bitcointalk\Models\Settings;
use CryptoPolice\Bitcointalk\Classes\CronJobs\Crawler;

/**
 * Topik Back-end Controller
 */
class Topic extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('CryptoPolice.Bitcointalk', 'bitcointalk', 'topic');
    }

    public function onScraping()
    {
        $memory_usage_profiling = Settings::get('memory_usage_profiling');

        if($memory_usage_profiling) {

            $mem_start = memory_get_usage();

        }

        $crawler = new Crawler(input('ids'));

        $crawler->run();

        if($memory_usage_profiling) {

            trace_log("Scraping script memory usage: ".(memory_get_usage()-$mem_start-sizeof($mem_start))." bytes");
        }

        return true;

    }
}
