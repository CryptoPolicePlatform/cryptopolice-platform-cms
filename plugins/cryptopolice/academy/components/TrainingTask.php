<?php namespace CryptoPolice\Academy\Components;

use Flash;
use Cms\Classes\ComponentBase;
use CryptoPolice\Academy\Models\Training;
use CryptoPolice\Academy\Models\TrainingCategory as TrainingCategory;

class TrainingTask extends ComponentBase
{

    public $task;
    public $categorySlug;

    public function componentDetails()
    {
        return [
            'name' => 'Training Task',
            'description' => 'Training Task for officer.'
        ];
    }

    public function onRun()
    {
        $task = Training::where('slug', $this->param('slug'))->first();

        if (!$task) {
            return $this->controller->run('404');
        }

        $categorySlug = TrainingCategory::where('id', $task->category_id)->value('slug');

        $this->task = $task;
        $this->categorySlug = $categorySlug;
    }

}
