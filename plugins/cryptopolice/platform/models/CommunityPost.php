<?php namespace CryptoPolice\Platform\Models;

use Carbon\Carbon;
use Model, Request, ValidationException;

/**
 * Model
 */
class CommunityPost extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Sluggable;
    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    /**
     * @var array Validation rules
     */
    public $rules = [
        'post_title'        => 'required|min:0|max:255',
        'post_description'  => 'required|min:0|max:10000',
    ];

    /*
     * Relations
     */
    public $belongsTo = [

        'user' => [
            'Rainlab\user\Models\User',
            'key' => 'user_id'
        ],
    ];

    public $hasMany = [

        'comments' => [
            'CryptoPolice\platform\Models\CommunityComment',
            'key'   => 'post_id',
            'order' => 'created_at desc'
        ],

        'views' => [
            'CryptoPolice\platform\Models\CommunityPostViews',
            'key'   => 'post_id'
        ],

    ];

    public $attachOne = [
        'post_image' => 'System\Models\File'
    ];

    /**
     * @var array Generate slugs for these attributes.
     */
    protected $slugs = ['slug' => 'post_title'];


    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_platform_community_posts';

    public function beforeSave()
    {
        
        if($this->deferredBindingCache->isEmpty()) {
            throw new ValidationException([
                'error' => 'Click Upload images to add your image'
            ]);
        }
        // Force creation of slug
        if (empty($this->slug)) {
            unset($this->slug);
            $this->setSluggedValue('slug', 'post_title');
        }
    }

    public function afterCreate()
    {
        if ($this->pin) {

            // Send notifications to all users about new post
            $notify = new Notification();
            $notify->title = $this->post_title;
            $notify->description = $this->post_description;
            $notify->announcement_at = Carbon::now();
            $notify->user_id = 0;
            $notify->save();

            // Send email to all users about new post
            // $users = User::all();
            // foreach ($users as $user) {
            //     $this->sendMail($user);
            // }
        }
    }

    public function sendMail($user)
    {
        $vars = [
            'message'   => $this->description,
            'title'     => $this->title,
            'name'      => $user->full_name,
            'mail'      => $user->email
        ];

        Mail::send('cryptopolice.bounty::mail.pin-notification', $vars, function ($message) use ($user) {
            $message->to($user->email, $user->full_name)->subject('New Notification');
        });

    }
}
