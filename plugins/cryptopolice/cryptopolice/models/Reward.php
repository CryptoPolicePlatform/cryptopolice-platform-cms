<?php namespace CryptoPolice\CryptoPolice\Models;

use Model;

class Reward extends Model
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
        'bounty_campaigns' => [
            'CryptoPolice\CryptoPolice\Models\Bounty',
            'key' => 'bounty_campaigns_id'
        ],
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_cryptopolice_rewards';
}
