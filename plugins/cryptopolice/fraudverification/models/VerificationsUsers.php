<?php namespace CryptoPolice\FraudVerification\Models;

use Model;

/**
 * Model
 */
class VerificationsUsers extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    /*
    * Relations
    */
    public $belongsTo = [

        'user' => [
            'Rainlab\user\Models\User',
            'key' => 'user_id'
        ],
        'application' => [
            'CryptoPolice\FraudVerification\Models\Application',
            'key' => 'application_id'
        ],
        'verdict' => [
            'CryptoPolice\FraudVerification\Models\Verdict',
            'key' => 'verdict_id'
        ],
        'level' => [
            'CryptoPolice\FraudVerification\Models\VerficationLevels',
            'key' => 'level_id'
        ],

    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_fraudverification_verification_users';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
