<?php namespace CryptoPolice\Bitcointalk\Classes;

use Log;
use Xparse\ElementFinder\ElementFinder;
use Xparse\CssExpressionTranslator\CssExpressionTranslator;

use Illuminate\Database\QueryException;

class Content
{
    private $finder;
    private $page;
    private $collection = [];

    public function __construct($page, $css = false)
    {
        $this->page = $page;

        $finder = new ElementFinder($page->html);

        if($css){
            $finder->setExpressionTranslator(new CssExpressionTranslator());
        }

        $this->finder = $finder;
    }

    public function parse()
    {
        $items =$this->finder->object('#quickModForm .bordercolor>tr');

        foreach ($items as $item){

            $content =  [
                'user_nick'         => $item->html('.poster_info a:first-child')->getFirst(),
                'user_profil'       => $item->html('.poster_info a:first-child @href')->getFirst(),
                'publication_date'  => $item->value('.td_headerandpost div.smalltext')->getFirst(),
                'content'           => json_encode($item->value('.post')->getFirst()),
                'content_raw'       => json_encode($item->html('.post')->getFirst()),
            ];

            if($content['user_nick'] && $content['content']) {
                $content['hash'] = md5(mb_convert_encoding($content['content'] . $content['user_profil'], 'UTF-8'));
                $this->collection[] = $content;
            }
        }

        return $this;
    }

    public function save()
    {
        $result = [];

        foreach ($this->collection as $item){

            try {
                $result[] = $this->page->contents()
                    ->updateOrCreate([
                        'hash'                  => $item['hash']
                    ],[
                        'user_nick'             => mb_convert_encoding($item['user_nick'], 'UTF-8'),
                        'user_profil'           => mb_convert_encoding($item['user_profil'], 'UTF-8'),
                        'publication_date'      => mb_convert_encoding($item['publication_date'], 'UTF-8'),
                        'content'               => mb_convert_encoding($item['content'], 'UTF-8'),
                        'content_raw'           => mb_convert_encoding($item['content_raw'], 'UTF-8'),
                    ]);

            } catch (QueryException $e) {
                Log::error($e->getMessage());
                trace_log(json_encode($item));
            }
        }

        if(!$this->page->title){
            $this->page->title = $this->finder->value('title')->getFirst();
            $this->page->save();
        }

        return $result;
    }
}