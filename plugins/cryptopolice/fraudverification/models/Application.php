<?php namespace CryptoPolice\FraudVerification\Models;

use Model;
use CryptoPolice\FraudVerification\Components\Officer as Officer;
use Log;

/**
 * Model
 */
class Application extends Model
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
        'type' => [
            'CryptoPolice\FraudVerification\Models\ApplicationTypes',
            'key' => 'type_id'
        ],

    ];

    public function afterCreate()
    {
        if($this->status){
            // Send to verification
           $verify =  Officer::SendToVerification($this->user_id, $this->id, null,1);

           Log::info('Application A'.$this->id.' sanded to verification. Officers -  '. $verify);
        }

    }

    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_fraudverification_application';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
