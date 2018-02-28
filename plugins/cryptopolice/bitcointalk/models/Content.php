<?php namespace CryptoPolice\Bitcointalk\Models;

use Model;

/**
 * Content Model
 */
class Content extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_bitcointalk_contents';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['hash', 'meta', 'publication_date', 'user_nick', 'content_raw', 'content', 'user_profil'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];

    public $morphTo = [
        'contentable' => []
    ];

    public $morphOne = [
        'page'  => ['CryptoPolice\Bitcointalk\Models\Page', 'name' => 'contentable']
    ];

    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function setContentRawAttribute($value)
    {
        $this->attributes['content_raw'] = htmlentities($value,  ENT_QUOTES | ENT_IGNORE | ENT_XHTML, "UTF-8");
    }

    public function getContentRawAttribute($value)
    {
        return json_decode(html_entity_decode($value, ENT_QUOTES | ENT_XHTML, "UTF-8"));
    }

    public function setContentAttribute($value)
    {
        $this->attributes['content'] = addslashes($value);
    }

    public function getContentAttribute($value)
    {
        return json_decode(stripslashes($value));
    }
}