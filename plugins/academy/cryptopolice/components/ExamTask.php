<?php namespace Academy\CryptoPolice\Components;

use Auth;
use Cache;
use Flash;
use DateTime;
use Redirect;
use DateInterval;
use Cms\Classes\ComponentBase;
use Illuminate\Support\Facades\Input;
use Academy\CryptoPolice\Models\Exam;
use Academy\CryptoPolice\Models\Score;
use Academy\CryptoPolice\Models\FinalScore;

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

    public function onCompleteTask()
    {

        $user = Auth::getUser();
        $task = $this->prepareFullExamTask();

        $userID = $user->id;
        $examID = $task[0]['id'];
        $examScore = $task[0]['s_score'];

        $query = FinalScore::where('exam_id', $examID)
            ->where('user_id', $userID)
            ->where('complete_status', '0')
            ->get()
            ->toArray();

        $try = isset($query[0]['try']) && !empty($query[0]['try']) ? $query[0]['try'] + 1 : '1';

        FinalScore::where('user_id', $userID)
            ->where('exam_id', $examID)
            ->where('try', $try)
            ->update([
                'complete_status' => '1',
                'score' => $examScore,
                'try' => $try
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

    $this->data = $questionNum;
        dd($questionNum, $answerNum);
        $correct = $task[0]['question'][$questionNum]['answers'][$answerNum]['answers_correct'];

        Score::insert([
            'scores' => '***/100',
            'user_id' => $userID,
            'exam_id' => $examID,
            'is_correct' => $correct,
            'answer_num' => $answerNum,
            'question_num' => $questionNum
        ]);

        return [
            $questionNum, $answerNum, $correct
        ];
    }


    /**
     *
     * Start Officer Exam
     *
     * - Get User identifier
     * - Preapare full task (questions + answers)
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

            $currentExamStatus = FinalScore::where('exam_id', $examID)
                ->where('user_id', $userID)
                ->where('complete_status', '0')
                ->first();

            // Verify if officer have completed previous exam
            if (isset($currentExamStatus->id) && !empty($currentExamStatus->id)) {

                $examStartTime = new DateTime('now');
                $examEndTime = new DateTime($currentExamStatus->completed_at);

                if ($examStartTime > $examEndTime) {

                    // Complete Task
                    $examScore = '4444';
                    $try = (isset($currentExamStatus->try) && !empty($currentExamStatus->try)) ? $currentExamStatus->try : 1;

                    FinalScore::where('user_id', $userID)
                        ->where('exam_id', $examID)
                        ->where('try', $try)
                        ->update([
                            'complete_status' => '1',
                            'score' => $examScore
                        ]);

                    return Redirect::to('/exam');

                } else {

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
                    FinalScore::insert(
                        [
                            'completed_at' => $examEndTime,
                            'created_at' => $examStartTime,
                            'exam_id' => $examID,
                            'user_id' => $userID,
                            'try' => $try
                        ]
                    );

                    //                    $currentExamStatus = FinalScore::where('exam_id', $examID)
                    //                        ->where('user_id', $userID)
                    //                        ->where('complete_status', '0')
                    //                        ->first();
                }

                $this->timer = $examEndTime->getTimestamp() - $examStartTime->getTimestamp();
                $this->fullTask = $task;

            } else {
                return Redirect::to('/exam');
            }
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
        $query = Exam::where('exam_slug', $slug)
            ->first();
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
