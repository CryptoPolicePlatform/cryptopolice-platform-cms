<?php namespace Academy\CryptoPolice\Components;

use Auth;
use Flash;
use Redirect;
use DateTime;
use Cms\Classes\ComponentBase;
use Academy\CryptoPolice\Models\Exam;
use Academy\CryptoPolice\Models\FinalScore;

class Exams extends ComponentBase
{

    public $exams;
    public $scores;
    
    public function componentDetails()
    {
        return [
            'name'        => 'Exam List',
            'description' => 'List of exams for officers.'
        ];
    }

    public function defineProperties()
    {
        return [
            'max' => [
                'description'       => 'The most amount of todo items allowed',
                'title'             => 'Max items',
                'default'           => 10,
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'The Max Items value is required and should be integer.'
            ]
        ];
    }

    public function onExamClick() {

    }

    /**
     * Displays a list of available exams.
     * - Check if user is logged in;
     * - Get exam list;
     * - Get user identifier;
     * - Get user current scores;
     */

    public function onRun() {

        // Check if user is logged in
        $loggedIn = Auth::check();
        if(!$loggedIn) {
            return Redirect::to('/login');
        }

        // Get exam list
        $exams = Exam::paginate(10);

        // Get user identifier
        $user = Auth::getUser();
        $user_id = $user->id;
       
        // Get user current scores
        $userScores = FinalScore::where('user_id', $user->id)->groupBy('exam_id')->orderBy('created_at','asc')->get()->toArray();

        if(!$exams) {
            return $this->controller->run('404');
        } else {
           $this->exams = $exams;
           $this->scores = $userScores;
       }
   }
}
