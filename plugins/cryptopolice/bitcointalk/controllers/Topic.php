<?php namespace CryptoPolice\Bitcointalk\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

use CryptoPolice\Bitcointalk\Classes\Topic as TopicClass;
use CryptoPolice\Bitcointalk\Classes\Scraper;

use CryptoPolice\Bitcointalk\Models\Content;

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
        $topik = new TopicClass(input('ids'));

        $collection = $topik->grabbing();

        foreach ($collection as $item){

            $scraper = new Scraper($item->pages()->get());
            $scraper->scraping();
        }

        return  true;
    }
}
