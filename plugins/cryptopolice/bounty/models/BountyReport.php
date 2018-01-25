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
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_bounty_user_reports';
}
