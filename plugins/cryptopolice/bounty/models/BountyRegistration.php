<?php namespace CryptoPolice\Bounty\Models;

use Mail;
use Model;
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

    public function afterUpdate() {

        if(isset($this->user_id) && !empty($this->user_id)) {

            $user = User::where('id', $this->user_id)->first();

            $vars = [
                'name' => $user->full_name,
                'mail' => $user->email,
            ];

            Mail::send('cryptopolice.bounty::mail.registration_bounty_message', $vars, function ($message) use ($user) {
                $message->to($user->email, $user->full_name)->subject('Bounty Campaign Registration');
            });
        }
        Flash::success('Mail ['.$user->email.'] has been send');
    }


}
