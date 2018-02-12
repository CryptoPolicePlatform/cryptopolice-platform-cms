<?php namespace CryptoPolice\Platform\Models;

use Model;

/**
 * Model
 */
class UserNotification extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_platform_users_notifications';
}
