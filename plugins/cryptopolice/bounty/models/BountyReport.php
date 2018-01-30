<?php namespace CryptoPolice\Bounty\Models;

use Model;

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
        'rewards' => [
            'CryptoPolice\bounty\Models\Reward',
            'key' => 'rewards_id'
        ],
        'user' => [
            'Rainlab\user\Models\User',
            'key' => 'user_id'
        ],
        'bounty_campaign' => [
            'CryptoPolice\bounty\Models\Bounty',
            'key' => 'bounty_campaigns_id'
        ],
        'bounty_user_registration' => [
            'CryptoPolice\bounty\Models\BountyRegistration',
            'key' => 'bounty_user_registration_id'
        ],
    ];

    protected $jsonable = ['description'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_bounty_user_reports';
}
