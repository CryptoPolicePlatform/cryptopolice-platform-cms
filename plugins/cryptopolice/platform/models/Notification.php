<?php namespace CryptoPolice\Platform\Models;

use Model;

/**
 * Model
 */
class Notification extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    use \October\Rain\Database\Traits\SoftDelete;


    protected $dates = ['deleted_at'];

    /**
     * @var array Validation rules
     */

    public $rules = [
        'title'             => 'required|min:0|max:255',
        'description'       => 'required'
    ];

    public $hasMany = [

        'users_notifications' => [
            'CryptoPolice\platform\Models\UserNotification',
            'key'   => 'notification_id',
            'order' => 'created_at desc'
        ],

    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_platform_notifications';

}
