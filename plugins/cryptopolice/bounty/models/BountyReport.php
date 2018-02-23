<?php namespace CryptoPolice\Bounty\Models;

use Mail;
use Flash;
use Model;
use ValidationException;
use October\Rain\Auth\Models\User;
use CryptoPolice\Platform\Models\Notification;

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
            'key' => 'reward_id'
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

        // Default reward type, as "None"
        $options['1'] = 'None';

        if ($rewards->isNotEmpty()) {
            foreach ($rewards as $key => $value) {
                if ($value->status) {

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
        $report = $data['BountyReport'];
        // Entered reward amount validation
        if (!preg_match('/^[0-9]+$/', $report['given_reward'])) {
            $message = 'Please, Enter valid number';
        }

        if (!isset($report['report_status'])) {
            throw new ValidationException([
                'message' => 'Please, select report status'
            ]);
        }

        // If selected type is "None"
        if ($report['reward'] == 1) {

            // If entered given reward is greater than zero
            if ($report['given_reward'] > 0) {
                $message = 'Reward should be 0 "Tokens" or "Stakes"';
            }

            // If selected reports status is approved
            if ($report['report_status'] == 1) {
                $message = 'Reports status should be selected as "Disapproved", because selected reward type is "None"';
            }
        }

        // If selected type is not "None"
        if ($report['reward'] > 1) {

            // If entered given reward is zero
            if ($report['given_reward'] == 0) {
                $message = 'Reward should be more the 0 "Tokens" or "Stakes", if selected type is not "None"';
            }

            // If selected reports status is disapproved
            if ($report['report_status'] == 2) {
                $message = 'Reports status should be selected as "Approved"';
            }
        }

        // Entered reward amount is greater than selected reward type minimum
        if (isset($this->reward->reward_amount_max)) {
            if ($report['given_reward'] > $this->reward->reward_amount_max) {
                $message = 'Entered reward amount (' . $report['given_reward'] . ') is higher then (' . $this->reward->reward_amount_max . ')';
            }
        }

        if (isset($this->reward->reward_amount_min)) {
            // Entered reward amount is greater than selected reward type maximum
            if ($report['given_reward'] < $this->reward->reward_amount_min) {
                $message = 'Given reward amount: ' . $report['given_reward'] . ' is less then (' . $this->reward->reward_amount_min . ')';
            }
        }

        if (isset($message) && !empty($message)) {
            throw new ValidationException([
                'message' => $message
            ]);
        }
    }

    public function getGivenRewardAttribute($keyValue = null)
    {

        if (isset($this->reward->reward_amount_max) && isset($this->original['given_reward'])) {

            if ($this->original['given_reward'] > 0) {
                return $this->original['given_reward'];
            }

            if ($this->reward->reward_amount_max == $this->original['given_reward']) {
                return $this->original['given_reward'];
            }

            if ($this->reward->reward_amount_max != $this->original['given_reward']) {
                return $this->reward->reward_amount_max;
            }
        } else {
            return true;
        }
    }


    public function afterUpdate()
    {

        if (isset($this->user_id) && !empty($this->user_id)) {

            $user = User::where('id', $this->user_id)->first();
            $campaign = Bounty::where('id', $this->bounty_campaigns_id)->first();

            // $this->sendMail($campaign, $user);
            $this->addUsersNotification($campaign);

            Flash::success('Mail & notification for [' . $user->email . '] has been send');

        } else {
            Flash::error('User is undefined');
        }
    }


    public function addUsersNotification($bounty)
    {
        $notify = new Notification();
        $notify->user_id = $this->user_id;
        $notify->title = 'Your ' . $bounty->title . ' Bounty campaign report was reviewed!';
        $notify->description = 'For more information please go to your CryptoPolice Bounty campaign profile.';
        $notify->save();
    }

    public function sendMail($campaign, $user)
    {
        $vars = [
            'name' => $user->nickname,
            'mail' => $user->email,
            'campaignTitle' => $campaign->title,
        ];
        Mail::send('cryptopolice.bounty::mail.report', $vars, function ($message) use ($user) {
            $message->to($user->email, $user->full_name)->subject('Bounty Campaign Report');
        });

    }
}
