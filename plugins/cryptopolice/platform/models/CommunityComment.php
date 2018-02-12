<?php namespace CryptoPolice\Platform\Models;

use Model;

/**
 * Model
 */
class CommunityComment extends Model
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

        'user' => [
            'Rainlab\user\Models\User',
            'key' => 'user_id'
        ],

        'post' => [
            'CryptoPolice\platform\Models\CommunityPost',
            'key' => 'post_id'
        ],

    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_platform_community_comment';
}
