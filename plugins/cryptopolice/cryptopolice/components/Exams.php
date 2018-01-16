<?php namespace CryptoPolice\CryptoPolice\Components;

use DB;
use Auth;
use Flash;
use Redirect;
use DateTime;
use Cms\Classes\ComponentBase;
use CryptoPolice\CryptoPolice\Models\Exam;
use CryptoPolice\CryptoPolice\Models\FinalScore;

class Exams extends ComponentBase
{

    public $exams;
    public $scores;

    public function componentDetails()
    {
        return [
            'name' => 'Exam List',
            'description' => 'List of exams for officers.'
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

    protected function formatDate($value)
    {
        return str_pad($value, 2, '0', STR_PAD_LEFT);
    }

    public function onExamClick()

    {

        $user = Auth::getUser();
        $userID = $user->id;
        $examID = post('id');
        $examSlug = post('slug');

        $exams = Exam::where('id', $examID)->first();
        $currentExamStatus = FinalScore::where('exam_id', $examID)
            ->where('user_id', $userID)
            ->orderBy('created_at', 'desc')
            ->first();

        // if latest exam not passed
        if (isset($currentExamStatus->complete_status) && ($currentExamStatus->complete_status == '0')) {
            return Redirect::to('/exam-task/' . $examSlug);
        }

        if (isset($currentExamStatus->complete_status) && ($currentExamStatus->complete_status == '1')) {

            $examStartTime = new DateTime('now');
            $examEndTime = new DateTime($currentExamStatus->completed_at);
            $timeSeconds = $examStartTime->getTimestamp() - $examEndTime->getTimestamp();

            if ($timeSeconds < $exams->retake_time) {

                $left = $exams->retake_time - $timeSeconds;
                $hours = floor($left / 3600);
                $minutes = floor(($left / 60) % 60);
                $seconds = $left % 60;
                Flash::error('You can retake your certification test again but you must wait! <br>' . $this->formatDate($hours) . ":" . $this->formatDate($minutes) . ":" . $this->formatDate($seconds));
            } else {
                return Redirect::to('/exam-task/' . $examSlug);
            }
        } else {
            return Redirect::to('/exam-task/' . $examSlug);
        }
    }

    /**
     * Displays a list of available exams.
     * - Check if user is logged in;
     * - Get exam list;
     * - Get user identifier;
     * - Get user current scores;
     */

    public function onRun()
    {

        // Check if user is logged in
        $loggedIn = Auth::check();
        if (!$loggedIn) {
            return Redirect::to('/login');
        }

        // Get exam list
        $exams = Exam::paginate(10);

        // Get user identifier
        $user = Auth::getUser();
        $user_id = $user->id;


        $userScores = FinalScore::where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        if (isset($exams->id) && !empty($exams->id)) {
            return $this->controller->run('404');
        } else {
            $this->exams = $exams;
            $this->scores = $userScores;
        }


        // TODO:: ja bi delal tak ...
//        $user = Auth::getUser();
//
//        if(empty($user)) {
//            return Redirect::to('/login');
//        } else {
//            // Get exam list
//            $exams = Exam::paginate(10);
//
//            if (isset($exams->id) && !empty($exams->id)) {
//                return $this->controller->run('404');
//            } else {
//                $this->exams = $exams;
//                $this->scores =  FinalScore::where('user_id', $user->id)
//                    ->orderBy('created_at', 'desc')
//                    ->get();
//            }
//        }


   }
}
