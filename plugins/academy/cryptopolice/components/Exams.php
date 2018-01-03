<?php namespace CryptoPolice\Newacademy\Components;

use Auth;
use Flash;
use Redirect;
use DateTime;
use Cms\Classes\ComponentBase;
use CryptoPolice\NewAcademy\Models\Exam;
use CryptoPolice\NewAcademy\Models\FinalScore;

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

    //     $id = post('id');
    //     $userScoress = $this->scores;
    //     $user = Auth::getUser();
    //     $user_id = $user->id;

    //     $userScores = FinalScore::where('exam_id', $id)
    //         ->where('user_id', $user_id)
    //         ->get()
    //         ->toArray();

    //         if($userScores) {
    //     $exam_started_at = new DateTime('now');
    //     $exam_ended_at = new DateTime($userScores[0]['complete_at']);
    //     if($exam_started_at > $exam_ended_at) {
    //         Flash::error('You can\'t pass this test!');
    //     }
    // }
    // return ['#test' => "
    // <p data-control=\"flash-message\" class=\"flash-message fade error\" data-interval=\"5\">First error<\/p>\n    
    // <p data-control=\"flash-message\" class=\"flash-message fade warning\" data-interval=\"5\">First warning<\/p>\n    
    // <p data-control=\"flash-message\" class=\"flash-message fade success\" data-interval=\"5\">First success</p>\n
    // "];

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
