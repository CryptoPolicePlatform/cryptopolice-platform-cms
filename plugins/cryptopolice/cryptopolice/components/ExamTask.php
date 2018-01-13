<?php namespace CryptoPolice\CryptoPolice\Components;

use Auth;
use Cache;
use Flash;
use DateTime;
use Redirect;
use DateInterval;
use Cms\Classes\ComponentBase;
use Illuminate\Support\Facades\Input;
use CryptoPolice\CryptoPolice\Models\Exam;
use CryptoPolice\CryptoPolice\Models\Score;
use CryptoPolice\CryptoPolice\Models\FinalScore;

class ExamTask extends ComponentBase
{

    public $timer;
    public $fullTask;
    public $data;

    public function componentDetails()
    {
        return [
            'name' => 'Exam Task',
            'description' => 'Exam for officers.'
        ];
    }


    /**
     * Complete current Exam Task
     * - Get user identifier
     * - Get task details from Cache
     * - Update record
     */

    public function onNextQuestion()
    {
        return true;
    }

    public function onClickQuestion()
    {
        return true;
    }

    public function onCompleteTask()
    {

        $user = Auth::getUser();
        $task = $this->prepareFullExamTask();

        $userID = $user->id;
        $examID = $task->id;

        $userTry = FinalScore::where('exam_id', $examID)
            ->where('user_id', $userID)
            ->where('complete_status', '0')
            ->first();
        $try = $userTry->try;

        $scores = Score::where('user_id', $userID)
            ->where('try', $try)
            ->where('exam_id', $examID)
            ->where('is_correct', '1')
            ->get();

        $correctAnswers = sizeof($scores);

        FinalScore::where('user_id', $userID)
            ->where('exam_id', $examID)
            ->where('try', $try)
            ->update([
                'complete_status' => '1',
                'score' => $correctAnswers,
                'try' => $try,
                'completed_at' => new DateTime('now')
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

        $answerNum = 0;
        $questionNum = 0;

        $answerNumber = 0;
        $answerCorrect = 0;

        $user = $this->getUserID();
        $task = $this->prepareFullExamTask();

        $examID = $task->id;
        $userID = $user->id;
        $questionID = Input::get('question_title');

        if (!empty($questionID)) {
            $arr = explode("_", $questionID);
            $questionNum = $arr[0] ? $arr[0] : 0;
            $answerNum = $arr[1] ? $arr[1] : 0;
        }

        foreach ($task['question'] as $key => $questions) {
            if ($questionNum == $key + 1) {
                foreach ($questions['answers'] as $ansKey => $answer) {
                    if ($answerNum == $answer['answer_number']) {
                        $answerNumber = $answer['answer_number'];
                        $answerCorrect = $answer['answer_correct'];
                    }
                }
            }
        }

        $userTry = FinalScore::where('exam_id', $examID)
            ->where('user_id', $userID)
            ->where('complete_status', '0')
            ->orderBy('created_at', 'desc')
            ->first();


        $answeredQuestion = Score::where('exam_id', $examID)
            ->where('user_id', $userID)
            ->where('question_num', $questionNum)
            ->where('try', $userTry->try)
            ->first();

        if (!$answeredQuestion) {
            Score::insert([
                'user_id' => $userID,
                'exam_id' => $examID,
                'answer_num' => $answerNumber,
                'question_num' => $questionNum,
                'is_correct' => $answerCorrect,
                'try' => $userTry->try,
                'created_at' => new DateTime('now')
            ]);
        }

        return [
            $questionNum, $answerNum, $answerCorrect
        ];
    }

    /**
     *
     * Start Officer Exam
     *
     * - Get User identifier
     * - Prepare full task (questions + answers)
     * - Trying to verify if officer have completed previous exam
     * - Check if current question is correct?
     * - Insert row
     */

    public function onRun()
    {

        $user = $this->getUserID();
        $task = $this->prepareFullExamTask();

        $userID = $user->id;
        $examID = $task->id;
        $examTimer = $task->timer;

        if (!empty($userID) && !empty($examID)) {

            // Get current ExamTask, not completed
            $currentExamStatus = FinalScore::where('exam_id', $examID)
                ->where('user_id', $userID)
                ->where('complete_status', '0')
                ->first();


            // Verify if officer have completed previous exam
            if (isset($currentExamStatus->id) && !empty($currentExamStatus->id)) {

                $examStartTime = new DateTime('now');
                $examEndTime = new DateTime($currentExamStatus->completed_at);

                // AutoComplete Task
                if ($examStartTime > $examEndTime) {
                    $try = (isset($currentExamStatus->try) && !empty($currentExamStatus->try)) ? $currentExamStatus->try : 1;

                    $scores = Score::where('user_id', $userID)
                        ->where('try', $try)
                        ->where('exam_id', $examID)
                        ->where('is_correct', '1')
                        ->get();
                    $correctAnswers = sizeof($scores);

                    FinalScore::where('user_id', $userID)
                        ->where('exam_id', $examID)
                        ->where('try', $try)
                        ->update([
                            'complete_status' => '1',
                            'score' => $correctAnswers
                        ]);

                    return Redirect::to('/exam');
                }

            } else {

                // Check if user can pass new exam
                $lastPassedExam = FinalScore::where('exam_id', $examID)
                    ->where('user_id', $userID)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if (!empty($lastPassedExam->id) && isset($lastPassedExam->id)) {

                    $examStartTime = new DateTime('now');
                    $examEndTime = new DateTime($lastPassedExam->created_at);
                    $timeSeconds = $examStartTime->getTimestamp() - $examEndTime->getTimestamp();

                    if ($timeSeconds < $task->retake_time) {
                        Flash::error('You can retake your certification test again but you must wait!');
                        return Redirect::to('/exam');
                    }

                }

                //Start New Exam
                $examStartTime = new DateTime('now');
                $examEndTime = new DateTime('now');
                $examEndTime->add(new DateInterval("PT{$examTimer}S"));

                // Get previous try number
                $try = FinalScore::where('exam_id', $examID)
                    ->where('user_id', $userID)
                    ->where('complete_status', '1')
                    ->orderBy('created_at', 'desc')
                    ->first();
                $try = isset($try->try) && !empty($try->try) ? $try->try + 1 : '1';

                // Use the next attempt "try"
                FinalScore::insert([
                        'completed_at' => $examEndTime,
                        'created_at' => $examStartTime,
                        'exam_id' => $examID,
                        'user_id' => $userID,
                        'try' => $try
                    ]
                );
            }
            $this->timer = $examEndTime->getTimestamp() - $examStartTime->getTimestamp();
            $this->fullTask = $task;

        } else {
            return Redirect::to('/exam');
        }
    }

    /**
     * Officer Exam
     * - Get Exam
     * - Return single row or null
     */

    public function prepareFullExamTask()
    {
        $slug = $this->param('slug');
        $query = Exam::where('exam_slug', $slug)->first();
        return $query ? $query : null;
    }

    /**
     * Get UserID
     */

    public function getUserID()
    {
        $user = Auth::getUser();
        return $user ? $user : null;
    }

}
