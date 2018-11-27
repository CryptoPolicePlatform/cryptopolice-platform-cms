<?php namespace CryptoPolice\FraudVerification\Models;

use Model;
use CryptoPolice\FraudVerification\Components\Officer as Officer;

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

    public function afterCreate()
    {
        if($this->status){
            // Send to verification

            Officer::SendToVerification($this->user_id, $this->application_id, $this->id,2);
        }
    }

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
