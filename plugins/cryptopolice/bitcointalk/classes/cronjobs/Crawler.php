<?php namespace CryptoPolice\Bitcointalk\Classes\CronJobs;

use Event;
use CryptoPolice\Bitcointalk\Classes\Topic;
use CryptoPolice\Bitcointalk\Classes\Scraper;

class Crawler
{
    private $topics;

    public function __construct($ids = null)
    {
        $this->topics = new Topic($ids);
    }

    public function run()
    {
        $collection = $this->topics->grabbing();

        foreach ($collection as $item){
            $scraper = new Scraper($item->pages()->get());
            $scraper->scraping();
        }

        Event::fire('bitcointalk.crawlEnd');

        return  true;
    }
}