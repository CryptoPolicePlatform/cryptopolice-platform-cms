<?php namespace CryptoPolice\Platform\Components;

use Flash;
use Cms\Classes\ComponentBase;
use CryptoPolice\Platform\Models\Scam;

class Scams extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Scam list',
            'description' => 'Scam list'
        ];
    }

    public function onRun()
    {

        $this->getScamStatistic();
        $this->page['scams'] = $this->getScams();
    }

    public function getScams()
    {
        return Scam::orderBy('created_at', 'desc')->paginate(10);
    }

    public function getScamStatistic()
    {
        $scams = Scam::get();

        $this->page['phishing']     = $scams->where('category', 1)->count();
        $this->page['scamming']     = $scams->where('category', 2)->count();

        $this->page['active']       = $scams->where('status', 1)->count();
        $this->page['offline']      = $scams->where('status', 0)->count();
    }

    public function onFilterScams()
    {

    }

    public function onAddScam()
    {

    }
}