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
        'description'  => 'required|min:0|max:1000',
        'title'        => 'required|min:0|max:255',
        'url'          => 'required|min:0|max:255'
    ];

    public $belongsTo = [
        'category' => 'CryptoPolice\Platform\Models\ScamCategory'
    ];

    protected $jsonable = ['fields_data'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_platform_scams';
}
