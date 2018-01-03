<?php namespace CryptoPolice\Newacademy\Components;

use Auth;
use Cache;
use Flash;
use DateTime;
use Redirect;
use DateInterval;
use Cms\Classes\ComponentBase;
use Illuminate\Support\Facades\Input;
use CryptoPolice\Newacademy\Models\Exam;
use CryptoPolice\Newacademy\Models\Score;
use CryptoPolice\Newacademy\Models\FinalScore;

class ExamTask extends ComponentBase
{
	
	public $timer;
	public $fullTask;

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

 	public function onCompleteTask() {

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

 		$user = Auth::getUser();
 		$task = $this->prepareFullExamTask();
 		$examID = $this->param('id');

 		$userID = $user->id;
 		$questionID = Input::get('question_title');

 		if (!empty($questionID)) {
 			$val = explode("_", $questionID);
 			$questionNum = $val[0];
 			$answerNum = $val[1];
 		} else {
 			$questionNum = 0;
 			$answerNum = 0;
 		}

 		$correct = $task[0]['question'][$questionNum]['answers'][$answerNum]['answers_correct'];

 		Score::insert([
 			'scores' => '***/100',
 			'user_id' => $userID,
 			'exam_id' => $examID,
 			'is_correct' => $correct,
 			'answer_num' => $answerNum,
 			'question_num' => $questionNum + 1
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

		$user = Auth::getUser();
		$task = $this->prepareFullExamTask();

		$userID = $user->id;
		$examID = $task[0]['id'];
		$timer = $task[0]['timer'];


		if(!empty($userID) && !empty($examID)) {

			$currentExamStatus = FinalScore::where('exam_id', $examID)
				->where('user_id', $userID)
				->where('complete_status', '0')
				->get()
				->toArray();

			// Trying to verify if officer have completed previous exam
			if(isset($currentExamStatus[0]['id']) && !empty($currentExamStatus[0]['id'])) {

				$examStartTime = new DateTime('now');
				$examEndTime = new DateTime($currentExamStatus[0]['complete_at']);

				if($examStartTime > $examEndTime) {

					$examScore = '4444';
					$try = (isset($currentExamStatus[0]['try']) && !empty($currentExamStatus[0]['try'])) ? $currentExamStatus[0]['try'] : 1;
					
					FinalScore::where('user_id', $userID)
						->where('exam_id', $examID)
						->where('try', $try)
						->update([
							'complete_status' => '1', 
							'score' => $examScore
						]);

					return Redirect::to('/exam');
				}

			} else {

				$examStartTime =  new DateTime('now');
				$examEndTime = new DateTime('now');
				$examEndTime->add(new DateInterval("PT{$timer}S"));

 				// Get previous try number
				$try = FinalScore::where('exam_id', $examID)
					->where('user_id', $userID)
					->where('complete_status', '1')
					->orderBy('created_at', 'desc')
					->get()
					->toArray();

				$try = isset($try[0]['try']) && !empty($try[0]['try']) ? $try[0]['try'] + 1 : '1';

		 		// Use the next attempt "try"
				FinalScore::insert(
					[
						'exam_id' => $task[0]['id'], 
						'user_id' => $user->id,
						'created_at' => $examStartTime,
						'complete_at' => $examEndTime,
						'try' => $try
					]
				);

 				//
				$currentExamStatus = FinalScore::where('exam_id', $task[0]['id'])
					->where('user_id', $user->id)
					->where('complete_status', '0')
					->get()
					->toArray();
			}

			$this->timer = $examEndTime->getTimestamp() - $examStartTime->getTimestamp();

			$this->fullTask = $task;
		}
	}




	public function prepareFullExamTask() {    

		$minutes = 20;
		$slug = $this->param('slug');

		Cache::flush();
		if (!Cache::has($slug)) {
			return Cache::remember($slug, $minutes, function() {
				return Exam::where('exam_slug', $this->param('slug'))->get()->toArray();
			});
		} else {
			return Cache::get($slug);
		}

	}
}
