<?php namespace CryptoPolice\Platform\Components;

use Auth, Flash, Session;
use Cms\Classes\ComponentBase;
use CryptoPolice\Platform\Models\Scam;
use CryptoPolice\Platform\Models\ScamCategory;
use CryptoPolice\Academy\Components\Recaptcha;

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
        if ($this->page->url == '/new-scam') {
            $this->page['categories'] = $this->getScamCategories();
        } else {
            $this->page['scams'] = $this->getScams();
        }
        $this->getScamStatistic();
    }

    public function getScams()
    {
        return Scam::orderBy('created_at', 'desc')->paginate(30);
    }

    public function getScamCategories()
    {
        return ScamCategory::get();
    }

    public function getScamStatistic()
    {

        $scams = Scam::get();

        $this->page['total_scams'] = $scams->count();

        $this->page['phishing']     = $scams->where('category_id', 1)->count();
        $this->page['scamming']     = $scams->where('category_id', 2)->count();
        $this->page['active']       = $scams->where('status', 1)->count();
        $this->page['offline']      = $scams->where('status', 0)->count();

        $this->page['percentage_phishing']      = $this->setPercentageValue($this->page['total_scams'], $this->page['phishing']);
        $this->page['percentage_scamming']      = $this->setPercentageValue($this->page['total_scams'], $this->page['scamming']);
        $this->page['percentage_active']        = $this->setPercentageValue($this->page['total_scams'], $this->page['active']);
        $this->page['percentage_offline']       = $this->setPercentageValue($this->page['total_scams'], $this->page['offline']);
    }

    public function setPercentageValue($total, $amount)
    {
        return (100 / $total * $amount) / 100;
    }

    public function onFilterScams()
    {
        $this->page['scams'] = Scam::Where(function ($query) {

            if (!empty(post('scam_category'))) {
                $query->where('category_id', post('scam_category'));
            }

            if (!empty(post('scam_status'))) {
                $query->where('status', post('scam_status'));
            }

        })->orderBy('created_at', 'desc')
            ->paginate(30);
    }

    public function onAddScam()
    {
        Recaptcha::verifyCaptcha();

        if (input('_token') == Session::token()) {

            $user = Auth::getUser();

            $scam = new Scam();

            $scam->description      = post('description');
            $scam->category         = post('category');
            $scam->title            = post('title');
            $scam->url              = post('url');
            $scam->user_id          = $user->id;
            $scam->save();

            Flash::success('Scam has been successfully added');

            return redirect()->back();
        }
    }
}