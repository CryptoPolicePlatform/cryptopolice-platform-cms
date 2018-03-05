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

    public $belongsTo = [

        'notification' => [
            'CryptoPolice\Platform\Models\Notification',
            'key' => 'notification_id'
        ],

        'user' => [
            'Rainlab\user\Models\User',
            'key' => 'user_id'
        ],

    ];


    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_platform_users_notifications';
}
