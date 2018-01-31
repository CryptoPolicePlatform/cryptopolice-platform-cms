<?php namespace CryptoPolice\Bounty\Models;

use Model;

class Bounty extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SoftDelete;
    use \October\Rain\Database\Traits\Sortable;

    protected $dates = ['deleted_at'];

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    /**
     * @var array Relations
     */
    public $hasMany = [

        'rewards' => [
            'CryptoPolice\bounty\Models\Reward',
            'key' => 'bounty_campaigns_id'
        ],

        'bountyReports' => [
            'CryptoPolice\bounty\Models\BountyReport',
            'key' => 'bounty_campaigns_id'
        ],

        'bountyRegistrations' => [
            'CryptoPolice\bounty\Models\BountyRegistration',
            'key' => 'bounty_campaigns_id'
        ]
    ];

    protected $jsonable = ['fields'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_bounty_campaigns';
}
