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

        $this->page['total_scams'] = $scams->count();

        $this->page['phishing']     = $scams->where('category', 1)->count();
        $this->page['scamming']     = $scams->where('category', 2)->count();
        $this->page['active']       = $scams->where('status', 1)->count();
        $this->page['offline']      = $scams->where('status', 0)->count();

        $this->page['percentage_phishing']  = $this->setPercentageValue($this->page['total_scams'], $this->page['phishing']);
        $this->page['percentage_scamming']  = $this->setPercentageValue($this->page['total_scams'], $this->page['scamming']);
        $this->page['percentage_active']    = $this->setPercentageValue($this->page['total_scams'], $this->page['active']);
        $this->page['percentage_offline']   = $this->setPercentageValue($this->page['total_scams'], $this->page['offline']);
    }

    public function setPercentageValue($total, $amount) {
        return (100 / $total * $amount) / 100;
    }

    public function onFilterScams()
    {

    }

    public function onAddScam()
    {

    }
}