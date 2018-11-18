<?php namespace CryptoPolice\FraudVerification\Models;

use Model;

/**
 * Model
 */
class Verdict extends Model
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
        'verification' => [
            'CryptoPolice\FraudVerification\Models\VerficationLevels',
            'key' => 'verification_id'
        ],
        'verdict' => [
            'CryptoPolice\FraudVerification\Models\ApplicationVerdicts',
            'key' => 'verdict_type_id'
        ],
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_fraudverification_verdict';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
