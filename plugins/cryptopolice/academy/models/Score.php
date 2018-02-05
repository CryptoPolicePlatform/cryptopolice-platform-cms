<?php namespace CryptoPolice\Academy\Models;

use Model;

class Score extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    /**
     * @var array Relations
     */
    public $belongsTo = [

        'exam' => [
            'CryptoPolice\Academy\Models\Exam',
            'key' => 'exam_id'
        ],

        'user' => [
            'Rainlab\user\Models\User',
            'key' => 'user_id'
        ],

    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_academy_scores';
}
