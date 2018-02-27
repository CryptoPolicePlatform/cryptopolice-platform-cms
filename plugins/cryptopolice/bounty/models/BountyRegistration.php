<?php namespace CryptoPolice\Bounty\Models;

use CryptoPolice\Platform\Models\Notification;
use Mail;
use Model;
use Flash;
use RainLab\User\Models\User;

/**
 * Model
 */
class BountyRegistration extends Model
{
    use \October\Rain\Database\Traits\Validation;

    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    protected $jsonable = ['fields_data'];

    /**
     * @var array Relations
     */
    public $hasMany = [
        'BountyReport' => ['CryptoPolice\bounty\Models\BountyReport']
    ];

    public $belongsTo = [

        'user' => [
            'Rainlab\user\Models\User',
            'key' => 'user_id'
        ],

        'bounty' => [
            'CryptoPolice\bounty\Models\Bounty',
            'key' => 'bounty_campaigns_id'
        ]

    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_bounty_user_registration';

    public function afterUpdate()
    {

        if (isset($this->user_id) && !empty($this->user_id)) {

            $user = User::where('id', $this->user_id)->first();
            $campaign = Bounty::where('id', $this->bounty_campaigns_id)->first();

            $this->addUsersNotification($campaign);
             $this->sendMail($user);
            Flash::success('Mail & notification for [' . $user->email . '] has been send');

        } else {
            Flash::error('User is undefined');
        }
    }


    public function addUsersNotification($bounty)
    {
        $notify = new Notification();
        $notify->user_id = $this->user_id;
        $notify->title = 'Thank you for your registration in CryptoPolice ' . $bounty->title ?: $bounty->title. ' Bounty campaign';
        $notify->description = 'Now you can make you reports basing on the conditions of the campaign';
        $notify->save();
    }

    public function sendMail($user)
    {
        $vars = [
            'name' => $user->nickname,
            'mail' => $user->email
        ];

        Mail::send('cryptopolice.bounty::mail.registration', $vars, function ($message) use ($user) {
            $message->to($user->email, $user->nickname)->subject('Bounty Campaign Registration');
        });

    }
}