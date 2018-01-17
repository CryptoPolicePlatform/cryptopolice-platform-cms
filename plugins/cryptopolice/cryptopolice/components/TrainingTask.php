<?php namespace CryptoPolice\CryptoPolice\Components;

use Flash;
use Cms\Classes\ComponentBase;
use CryptoPolice\CryptoPolice\Models\Training;
use Cryptopolice\Cryptopolice\Models\TraningCategory as TraningCategory;

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

    public function onAcceptTraining()
    {
        Flash::success('test');
    }

    public function onRun()
    {

        $slug = $this->param('slug');
        $task = Training::where('slug', $slug)->first();

        $categorySlug = TraningCategory::where('id', $task->category_id)->value('slug');

        if (!$task) {
            return $this->controller->run('404');
        } else {
            $this->task = $task;
            $this->categorySlug = $categorySlug;
        }
    }

}
