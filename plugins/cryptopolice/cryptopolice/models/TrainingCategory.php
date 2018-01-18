<?php namespace CryptoPolice\CryptoPolice\Models;

use Model;

class TrainingCategory extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SoftDelete;
    use \October\Rain\Database\Traits\Sortable;

    protected $dates = ['deleted_at'];

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public $hasMany = [
        'Training' => ['CryptoPolice\CryptoPolice\Models\Training'],
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_cryptopolice_trainings_category';
}
