<?php namespace CryptoPolice\CryptoPolice\Components;

use DB;
use Auth;
use Flash;
use Redirect;
use DateTime;
use Cms\Classes\ComponentBase;
use CryptoPolice\CryptoPolice\Models\Exam;
use CryptoPolice\CryptoPolice\Models\FinalScore;
use CryptoPolice\CryptoPolice\Models\Score;

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
        $slug = post('slug');

        $selectedExam = Exam::where('id', $examID)->first();

        $selectedExamStatus = FinalScore::where('exam_id', $examID)
            ->where('user_id', $userID)
            ->orderBy('created_at', 'desc')
            ->first();

        // if latest exam not passed
        if (isset($selectedExamStatus->complete_status) && ($selectedExamStatus->complete_status == '0')) {
            return Redirect::to('/exam-task/' . $slug);
        }

        if (isset($selectedExamStatus->complete_status) && ($selectedExamStatus->complete_status == '1')) {

            $start = new DateTime('now');
            $end = new DateTime($selectedExamStatus->completed_at);
            $left = $start->getTimestamp() - $end->getTimestamp();

            if ($left < $selectedExam->retake_time) {

                $timeLeft = $selectedExam->retake_time - $left;
                Flash::error('You can retake your certification test again but you must wait! <br>' . $this->getDate($timeLeft));

            } else {
                return Redirect::to('/exam-task/' . $slug);
            }
        } else {
            return Redirect::to('/exam-task/' . $slug);
        }
    }



    public function getDate($time)
    {
        $hours = floor($time / 3600);
        $minutes = floor(($time / 60) % 60);
        $seconds = $time % 60;
        return $this->formatDate($hours) . ":" . $this->formatDate($minutes) . ":" . $this->formatDate($seconds);
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

        $examList = Exam::paginate(10);

        $user = Auth::getUser();
        $userID = $user->id;

        $userScores = FinalScore::where('user_id', $userID)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($examList) {
            $this->exams = $examList;
            $this->scores = $userScores;
        } else {
            $this->exams = false;
        }
    }
}
