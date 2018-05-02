<?php namespace cryptopolice\airdrop\Models;

use Model;

/**
 * Model
 */
class Airdrop extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    public $jsonable = ['fields'];
    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_airdrop_airdrop';
}
