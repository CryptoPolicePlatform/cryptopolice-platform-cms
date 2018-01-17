<?php namespace CryptoPolice\CryptoPolice\Components;

use Redirect;
use Response;
use Cms\Classes\ComponentBase;
use CryptoPolice\CryptoPolice\Models\Training;
use CryptoPolice\CryptoPolice\Models\TrainingCategory;

class Trainings extends ComponentBase
{

    public $slug;
    public $trainings;
    public $trainings_category;

    public function componentDetails()
    {
        return [
            'name' => 'Training List',
            'description' => 'Training List of tasks.'
        ];
    }

    public function defineProperties()
    {
    }

    public function onRun()
    {
        $this->slug = $slug = $this->param('slug');

        if ($slug) {
            // Get trainings  by category slug
            $category_id = TrainingCategory::where('slug', $slug)->value('id');
            $trainings = Training::where('category_id', $category_id)->paginate(10);
        } else {
            // Get all trainings category
            $trainings = TrainingCategory::where('status', 1)->paginate(10);
        }

        $this->trainings = $trainings;
    }

}
