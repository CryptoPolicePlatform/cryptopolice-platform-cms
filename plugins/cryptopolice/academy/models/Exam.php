<?php namespace CryptoPolice\Academy\Models;

use Model;

class Exam extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    protected $jsonable = ['question'];

    public $belongsTo = [

        'scores' => [
            'CryptoPolice\Academy\Models\Score',
            'key' => 'exam_id'
        ],

        'finalScore' => [
            'CryptoPolice\Academy\Models\FinalScore',
            'key' => 'exam_id'
        ],

    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_academy_exams';
}
