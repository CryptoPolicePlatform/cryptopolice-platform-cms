<?php namespace CryptoPolice\Bitcointalk\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

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
        $crawler = new Crawler(input('ids'));

        return $crawler->run();
    }
}
