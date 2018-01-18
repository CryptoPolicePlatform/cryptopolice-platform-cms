<?php namespace CryptoPolice\CryptoPolice\Models;

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

    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_cryptopolice_exams';
}
