<?php namespace CryptoPolice\Platform\Models;

use Model;

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
        'post_title' => 'required|min:0|max:255',
        'post_description' => 'required|min:0|max:10000'
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
        // Force creation of slug
        if (empty($this->slug)) {
            unset($this->slug);
            $this->setSluggedValue('slug', 'post_title');
        }
    }
}
