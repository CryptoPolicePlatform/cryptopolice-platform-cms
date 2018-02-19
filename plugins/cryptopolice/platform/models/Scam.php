<?php namespace CryptoPolice\Platform\Models;

use Model;

/**
 * Model
 */
class Scam extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    /**
     * @var array Validation rules
     */
    public $rules = [
        'title' => 'required|min:0|max:255',
        'description' => 'required|min:0|max:10000'
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_platform_scams';
}
