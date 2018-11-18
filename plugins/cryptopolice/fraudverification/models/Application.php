<?php namespace CryptoPolice\FraudVerification\Models;

use Model;

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
