<?php namespace CryptoPolice\Platform\Models;

use Model;

/**
 * Model
 */
class CommunityPostViews extends Model
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
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_platform_community_post_views';
}
