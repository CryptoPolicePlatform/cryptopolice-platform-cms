<?php namespace CryptoPolice\Academy\Components;

use Auth, Flash, Redirect, Response;
use Cms\Classes\ComponentBase;
use CryptoPolice\Academy\Models\Training;
use CryptoPolice\Academy\Models\TrainingView;
use CryptoPolice\Academy\Models\TrainingCategory;

class Trainings extends ComponentBase
{

    public $slug;

    public function componentDetails()
    {
        return [
            'name'          => 'Training List',
            'description'   => 'Training List of tasks.'
        ];
    }

    public function onRun()
    {
        $this->page['slug'] = $slug = $this->param('slug');

        if ($slug) {

            // Get trainings  by category slug
            $category_id = TrainingCategory::where('slug', $slug)
                ->value('id');

            $trainings = Training::leftJoin('cryptopolice_academy_training_views as views', function ($join) {
                $join->on('cryptopolice_academy_trainings.id', '=', 'views.training_id')
                    ->where('views.user_id', Auth::getUser()->id);
            })
                ->select('cryptopolice_academy_trainings.*', 'views.training_id as watched')
                ->where('category_id', $category_id)
                ->orderBy('sort_order', 'asc')
                ->paginate(10);

        } else {

            // Get all trainings category
            $trainings = TrainingCategory::where('status', 1)
                ->orderBy('sort_order', 'asc')
                ->paginate(10);
        }
        $this->page['trainings'] = $trainings;
    }

    public function onTrainingCheck()
    {

        $trainingViews = TrainingView::where('user_id', Auth::getUser()->id)
            ->where('training_id', post('id'))
            ->get();

        if ($trainingViews->isEmpty()) {

            TrainingView::insert([
                'user_id'       => Auth::getUser()->id,
                'training_id'   => post('id')
            ]);
        }

        return Redirect::to('training-task/' . post('slug'));
    }
}
