<?php namespace CryptoPolice\Bounty\Models;

use Model;
use ValidationException;

/**
 * Model
 */
class BountyReport extends Model
{
    use \October\Rain\Database\Traits\Validation;

    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    /**
     * @var array Relations
     */
    public $belongsTo = [

        'reward' => [
            'CryptoPolice\bounty\Models\Reward',
            'key' => 'rewards_id'
        ],

        'user' => [
            'Rainlab\user\Models\User',
            'key' => 'user_id'
        ],

        'bounty' => [
            'CryptoPolice\bounty\Models\Bounty',
            'key' => 'bounty_campaigns_id'
        ],

        'userRegistration' => [
            'CryptoPolice\bounty\Models\BountyRegistration',
            'key' => 'bounty_user_registration_id'
        ]

    ];


    protected $jsonable = ['description'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_bounty_user_reports';

    public function getRewardOptions($keyValue = null)
    {

        $options = [];
        $rewards = Reward::where('bounty_campaigns_id', $this->bounty_campaigns_id)->get();

        $options[0] = 'None';
        if ($rewards->isNotEmpty()) {
            foreach ($rewards as $key => $value) {
                if($value->status) {

                    $type = $value->reward_type ? 'Stakes' : 'Tokens';
                    $interval = $value->reward_amount_min ? $value->reward_amount_min . ' - ' . $value->reward_amount_max : $value->reward_amount_max;
                    $options[$value->id] = $value->reward_title . " [ " . $type . ' ' . $interval . " ] ";

                }
            }
        }
        return $options;
    }

    public function beforeSave()
    {

        $data = input();

        // Check negative number

        if($data['BountyReport']['given_reward'] < 0) {
            throw new ValidationException([
                'message' => 'Please exclude negative numbers'
            ]);
        }


        // Check if entered reward is higher than specified max and report status is approved

        if ($data['BountyReport']['given_reward'] > $this->reward->reward_amount_max && $data['BountyReport']['report_status']) {
            throw new ValidationException([
                'message' =>
                    'Entered : ' . $data['BountyReport']['given_reward'] . " higher then " . $this->reward->reward_amount_max
            ]);
        }

        // Check if entered reward is less then specified min and report status is approved

        if ($data['BountyReport']['given_reward'] < $this->reward->reward_amount_min && $this->reward->reward_amount_min != 0 && $data['BountyReport']['report_status']) {
            throw new ValidationException([
                'message' =>
                    'Given reward: ' . $data['BountyReport']['given_reward'] . " less then " . $this->reward->reward_amount_min . " ! "
            ]);
        }

        // Check if entered reward is equal to zero and report status is disapproved

        if ($data['BountyReport']['report_status'] == 2 && $data['BountyReport']['given_reward'] != 0) {
            throw new ValidationException([
                'message' => 'Report status is disapproved, given reward should be 0'
            ]);
        }
    }


    public function getGivenRewardAttribute($keyValue = null)
    {

    	if($this->original['given_reward']) {
    		return $this->original['given_reward'];
    	}
        
        if(isset($this->reward->reward_amount_max)) {
			return $this->reward->reward_amount_max;
        } else {
            return;
        }

        if (!empty($this->reward->reward_amount_max)) {
            return $this->reward->reward_amount_max;
        }
    }

}
