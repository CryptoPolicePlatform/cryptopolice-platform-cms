<?php namespace CryptoPolice\Academy\Components;

use Redirect;
use Response;
use Cms\Classes\ComponentBase;
use CryptoPolice\Academy\Models\Training;
use CryptoPolice\Academy\Models\TrainingCategory;

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

    public function onRun()
    {
        $this->slug = $slug = $this->param('slug');

        if ($slug) {

            // Get trainings  by category slug
            $category_id = TrainingCategory::where('slug', $slug)
                ->value('id');

            $trainings = Training::where('category_id', $category_id)
                ->orderBy('sort_order', 'asc')
                ->paginate(10);

        } else {

            // Get all trainings category
            $trainings = TrainingCategory::where('status', 1)
                ->orderBy('sort_order', 'asc')
                ->paginate(10);
        }

        $this->trainings = $trainings;
    }

}
