<?php namespace CryptoPolice\Platform\Components;

use Auth, Flash, Session, Validator;
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
        return Scam::where('status', 1)->orderBy('created_at', 'desc')->paginate(30);
    }

    public function getScamCategories()
    {
        return ScamCategory::get();
    }

    public function onCategoryChange()
    {
        $this->page['fields'] = ScamCategory::where('id', post('category_id'))->value('fields');
    }

    public function getScamStatistic()
    {

        $scams = Scam::get();

        $this->page['total_scams'] = $scams->count();

        $this->page['phishing']     = $scams->where('category_id', 1)->count();
        $this->page['scamming']     = $scams->where('category_id', 2)->count();
        $this->page['active']       = $scams->where('active', 1)->count();
        $this->page['offline']      = $scams->where('active', 0)->count();

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
        $this->page['scams'] = Scam::Where(function ($fileds) {

            if (!empty(post('scam_category'))) {
                $fileds->where('category_id', post('scam_category'));
            }

            if (!empty(post('scam_status'))) {
                $fileds->where('active', post('scam_status'));
            }

        })->orderBy('created_at', 'desc')
            ->paginate(30);
    }

    public function prepareValidationRules()
    {

        $fields = ScamCategory::where('id', post('category'))->value('fields');

        foreach ($fields as $value) {
            $rules[$value['name']] = $value['regex'];
        }

        $validator = Validator::make(post(), $rules);

        if ($validator->fails()) {
            Flash::error($validator->messages()->first());
        } else {
            return true;
        }
    }


    public function onAddScam()
    {
        Recaptcha::verifyCaptcha();

        if (input('_token') == Session::token()) {

            if ($this->prepareValidationRules()){

            $user = Auth::getUser();

            $scam = new Scam();

            $scam->description = post('description');
            $scam->category = post('category');
            $scam->title = post('title');
            $scam->url = post('url');
            $scam->user_id = $user->id;
            $scam->save();

            Flash::success('Scam has been successfully added');

            return redirect()->back();
            }
        }
    }
}