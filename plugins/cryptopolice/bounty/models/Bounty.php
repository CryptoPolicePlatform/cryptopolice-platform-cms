<?php namespace CryptoPolice\Bounty\Models;

use Model;

class Bounty extends Model
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

    /**
     * @var array Relations
     */
    public $hasMany = [
        'Reward' => ['CryptoPolice\bounty\Models\Reward'],
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_bounty_campaigns';
}
