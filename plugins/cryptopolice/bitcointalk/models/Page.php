<?php namespace CryptoPolice\Bitcointalk\Models;

use Model;

/**
 * Page Model
 */
class Page extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_bitcointalk_pages';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['html', 'full_url', 'meta'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    
    public $belongsTo = [

    ];

    public $belongsToMany = [];

    public $morphTo = [
        'pageable' => []
    ];

    public $morphOne = [
        'topik'  => ['CryptoPolice\Bitcointalk\Models\Topic', 'name' => 'pageable']
    ];

    public $morphMany = [
        'contents'  => [
            'CryptoPolice\Bitcointalk\Models\Content',
            'table' => 'cryptopolice_bitcointalk_contents',
            'name' => 'contentable'
        ]
    ];

    public $morphedByMany = [];

    public $attachOne = [];
    public $attachMany = [];

    public function setHtmlAttribute($value)
    {
        $this->attributes['html'] = htmlentities($value,  ENT_QUOTES | ENT_IGNORE | ENT_XHTML, "UTF-8");
    }

    public function getHtmlAttribute($value)
    {
        return html_entity_decode($value, ENT_QUOTES | ENT_XHTML, "UTF-8");
    }
}
