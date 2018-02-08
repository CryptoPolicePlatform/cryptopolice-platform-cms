<?php namespace CryptoPolice\Platform\Models;

use Model;

/**
 * Model
 */
class CommunityPost extends Model
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
    ];

    public $attachOne = [
        'post_image' => 'System\Models\File'
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_platform_community_posts';
}
