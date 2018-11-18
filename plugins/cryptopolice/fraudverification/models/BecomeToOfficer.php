<?php namespace CryptoPolice\FraudVerification\Models;

use Model;

/**
 * Model
 */
class BecomeToOfficer extends Model
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
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_fraudverification_become_to_officer';



    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
