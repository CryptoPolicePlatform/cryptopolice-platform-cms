<?php namespace CryptoPolice\CryptoPolice\Models;

use Model;

/**
 * Model
 */
class Training extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];


    public $belongsTo = [
        'category' => [
            'CryptoPolice\CryptoPolice\Models\TrainingCategory',
            'key' => 'category_id'
        ],
    ];


    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_cryptopolice_trainings';
}
