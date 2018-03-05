<?php namespace CryptoPolice\Academy\Components;

use Auth;
use Cache;
use Flash;
use Redirect;
use DateTime;
use DateInterval;
use Cms\Classes\ComponentBase;
use Illuminate\Support\Facades\Input;
use CryptoPolice\Academy\Models\Exam;
use CryptoPolice\Academy\Models\Score;
use CryptoPolice\Academy\Models\FinalScore;

class ExamTask extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Exam Task',
            'description' => 'Exam for officers.'
        ];
    }


    /**
     * Start Officer Exam
     * - Get User identifier
     * - Prepare full task (questions + answers)
     * - Trying to verify if officer have completed previous exam
     * - Check if current question is correct?
     * - Insert row
     */

    public function onRun()
    {

        $user = Auth::getUser();
        $selectedExam = $this->getSelectedExam();

        // Get the status of a non-finished exam
        $currentExamStatus = FinalScore::where('exam_id', $selectedExam->id)
            ->where('user_id', $user->id)
            ->where('complete_status', '0')
            ->first();

        // if non-finished exam
        if (($currentExamStatus)) {

            // Get the current attempt
            $try = $currentExamStatus->try;

            // Get the number of correct answers from query
            $scores = Score::where('user_id', $user->id)
                ->where('exam_id', $selectedExam->id)
                ->where('is_correct', '1')
                ->where('try', $try)
                ->get();
            $correctAnsCounter = sizeof($scores);

            // Complete the current exam
            FinalScore::where('user_id', $user->id)
                ->where('exam_id', $selectedExam->id)
                ->where('try', $try)
                ->update([
                    'score' => $correctAnsCounter,
                    'complete_status' => '1'
                ]);

            return Redirect::to('/exam');

        } else {

            // Get the previous passed exam
            $previousPassedExam = FinalScore::where('exam_id', $selectedExam->id)
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($previousPassedExam) {

                $now = new DateTime('now');
                $completeAt = new DateTime($previousPassedExam->created_at);

                //  Get time interval in seconds from the end of the exam
                $left = $now->getTimestamp() - $completeAt->getTimestamp();

                // If interval less then retake time
                if ($left < $selectedExam->retake_time) {
                    Flash::error('You can retake your certification test again but you must wait!');
                    return Redirect::to('/exam');
                }
            }

            // Start a new exam
            $now = new DateTime('now');

            // Completed_at = now + time for passing the exam
            $completeAt = new DateTime('now');
            $completeAt->add(new DateInterval("PT{$selectedExam->timer}S"));

            // Get the number of the previous attempt
            $try = FinalScore::where('exam_id', $selectedExam->id)
                ->where('user_id', $user->id)
                ->where('complete_status', '1')
                ->orderBy('created_at', 'desc')
                ->first();

            // if there was no previous attempt, so will be the first
            $try = isset($try->try) && !empty($try->try) ? $try->try + 1 : '1';

            // Adding information about the beginning of the exam
            FinalScore::insert([
                    'completed_at' => $completeAt,
                    'created_at' => $now,
                    'exam_id' => $selectedExam->id,
                    'user_id' => $user->id,
                    'try' => $try
                ]
            );
        }

        $this->page['timer'] = $completeAt->getTimestamp() - $now->getTimestamp();
        $this->page['fullTask'] = $selectedExam;
    }

    public function onNextQuestion()
    {
        return true;
    }

    public function onClickQuestion()
    {
        return true;
    }


    /**
     * Complete current Exam Task
     * - Get user identifier
     * - Get task details from Cache
     * - Update record
     */

    public function onCompleteTask()
    {

        $user = Auth::getUser();
        $selectedExam = $this->getSelectedExam();

        // Get the number of the current attempt
        $userTry = FinalScore::where('exam_id', $selectedExam->id)
            ->where('user_id', $user->id)
            ->where('complete_status', '0')
            ->first();

        $try = $userTry->try;

        // get correct answers for current exam
        $scores = Score::where('user_id', $user->id)
            ->where('try', $try)
            ->where('exam_id', $selectedExam->id)
            ->where('is_correct', '1')
            ->get();

        $correctAnswers = sizeof($scores);

        // Complete the current exam
        FinalScore::where('user_id', $user->id)
            ->where('exam_id', $selectedExam->id)
            ->where('try', $try)
            ->update([
                'complete_status'   => '1',
                'score'             => $correctAnswers,
                'try'               => $try,
                'completed_at'      => new DateTime('now')
            ]);

        return Redirect::to('/exam');
    }


    /**
     * Check question "is correct?"
     * - Get question ID
     * - Get answer ID
     * - Get user ID
     * - Check if current question is correct?
     * - Insert row
     */

    public function onCheckQuestion()
    {

        $user = Auth::getUser();
        $selectedExam = $this->getSelectedExam();

        $answerNumber    = 0;
        $answerCorrect    = 0;
        $selectedAnswer   = 0;
        $selectedQuestion = 0;

        // get from field question and selected answer
        $questionID = Input::get('question_title');
        if (!empty($questionID)) {
            $arr = explode("_", $questionID);
            $selectedQuestion  = $arr[0] ? $arr[0] : 0;
            $selectedAnswer    = $arr[1] ? $arr[1] : 0;
        }

        // Check the answer
        foreach ($selectedExam['question'] as $key => $questions) {
            if ($selectedQuestion == $key + 1) {
                foreach ($questions['answers'] as $ansKey => $answer) {
                    if ($selectedAnswer == $answer['answer_number']) {
                        $answerCorrect = $answer['answer_correct'];
                        $answerNumber  = $answer['answer_number'];
                    }
                }
            }
        }

        // get users try
        $userTry = FinalScore::where('exam_id', $selectedExam->id)
            ->where('user_id', $user->id)
            ->where('complete_status', '0')
            ->orderBy('created_at', 'desc')
            ->first();

        // Check for unique answer
        $answeredQuestion = Score::where('exam_id', $selectedExam->id)
            ->where('user_id', $user->id)
            ->where('question_num', $selectedQuestion)
            ->where('try', $userTry->try)
            ->first();

        // Insert new row if not unique
        if (!$answeredQuestion) {

            Score::insert([
                'created_at'    => new DateTime('now'),
                'question_num'  => $selectedQuestion,
                'is_correct'    => $answerCorrect,
                'answer_num'    => $answerNumber,
                'exam_id'       => $selectedExam->id,
                'user_id'       => $user->id,
                'try'           => $userTry->try
            ]);
        }

        return [
            $selectedQuestion, $selectedAnswer, $answerCorrect
        ];
    }

    public function getSelectedExam()
    {
        return Exam::where('exam_slug', $this->param('slug'))->first();
    }
}