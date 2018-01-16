<?php namespace CryptoPolice\CryptoPolice\Components;

use Redirect;
use Response;
use Cms\Classes\ComponentBase;
use CryptoPolice\CryptoPolice\Models\Training;
use CryptoPolice\CryptoPolice\Models\TraningCategory;


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
        return [
            'max' => [
                'description' => 'The most amount of todo items allowed',
                'title' => 'Max items',
                'default' => 10,
                'type' => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'The Max Items value is required and should be integer.'
            ]
        ];
    }
    
    public function onRun()
    {

        $this->slug = $slug = $this->param('slug');

        if ($slug) {
            // Get trainings  by category slug
            $category_id = TraningCategory::where('slug', $slug)->value('id');
            $trainings = Training::where('category_id', $category_id)->paginate(10);
        } else {
            // Get all trainings category
            $trainings = TraningCategory::where('status', 1)->paginate(10);
        }

        if ($trainings) {
            $this->trainings = $trainings;
        } else {
            $this->trainings = false;
        }

    }

}
