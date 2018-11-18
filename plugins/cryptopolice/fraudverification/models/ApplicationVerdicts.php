<?php namespace CryptoPolice\FraudVerification\Models;

use Model;

/**
 * Model
 */
class ApplicationVerdicts extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];


    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_fraudverification_application_verdicts';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
