<?php namespace CryptoPolice\Bitcointalk\Classes;

use Event;

use CryptoPolice\Bitcointalk\Models\Page;

use CryptoPolice\Bitcointalk\Classes\Content;

class Scraper
{
    private $pages;

    public function __construct($pages = null)
    {
        if($pages) {
            $this->pages = $pages;
        } else {
            $this->pages = Page::all();
        }
    }

    public function scraping()
    {
        if($this->pages->isEmpty()) {
            return null;
        }

        foreach ($this->pages as $page) {
            $content = new Content($page, true);
            $content->parse()->save();
        }

        Event::fire('bitcointalk.endScraping', [&$this->models]);

        return true;
    }
}