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


    /**
     * Displays a list of available exams.
     * - Get exam list;
     * - Get user current scores;
     */

    public function onRun()
    {
        $user = Auth::getUser();
        $examList = Exam::paginate(10);

        if ($examList) {
            $this->exams = $examList;
            $this->scores = FinalScore::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $this->exams = false;
        }
    }


    /**
     * When user clicks on exam link.
     * - Get exam;
     * - Get exam status;
     * - Check exam status
     */

    public function onExamClick()
    {

        $user = Auth::getUser();

        $selectedExam = Exam::where('id', post('id'))->first();

        $selectedExamStatus = FinalScore::where('exam_id', post('id'))
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();

        // if latest exam not passed
        if (isset($selectedExamStatus->complete_status) && ($selectedExamStatus->complete_status == '0')) {
            return Redirect::to('/exam-task/' . post('slug'));
        }

        // if can start new one
        if (isset($selectedExamStatus->complete_status) && ($selectedExamStatus->complete_status == '1')) {

            $start = new DateTime('now');
            $end = new DateTime($selectedExamStatus->completed_at);
            $left = $start->getTimestamp() - $end->getTimestamp();

            if ($left < $selectedExam->retake_time) {

                $timeLeft = $selectedExam->retake_time - $left;
                Flash::error('You can retake your certification test again but you must wait! <br>' . $this->getDate($timeLeft));

            } else {
                return Redirect::to('/exam-task/' . post('slug'));
            }
        } else {
            return Redirect::to('/exam-task/' . post('slug'));
        }
    }


    protected function formatDate($value)
    {
        return str_pad($value, 2, '0', STR_PAD_LEFT);
    }

    protected function getDate($time)
    {
        $hours = floor($time / 3600);
        $minutes = floor(($time / 60) % 60);
        $seconds = $time % 60;
        return $this->formatDate($hours) . ":" . $this->formatDate($minutes) . ":" . $this->formatDate($seconds);
    }

}
