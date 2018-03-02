<?php namespace CryptoPolice\Academy\Models;

use Model;

/**
 * Model
 */
class TrainingView extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    /**
     * @var array Relations
     */
    public $belongsTo = [

        'training' => [
            'CryptoPolice\Academy\Models\Training',
            'key' => 'training_id'
        ],

    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_academy_training_views';
}
