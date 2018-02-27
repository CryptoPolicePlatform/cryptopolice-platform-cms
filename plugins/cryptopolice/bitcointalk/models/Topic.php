<?php namespace CryptoPolice\Bitcointalk\Models;

use Model;
use \October\Rain\Database\Traits\Validation;
use \October\Rain\Database\Traits\Purgeable;
use October\Rain\Exception\ValidationException;

/**
 * Topik Model
 */
class Topic extends Model
{
    use Validation, Purgeable;

    protected $purgeable = ['host'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_bitcointalk_topics';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    public $host = 'bitcointalk.org';

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['url'];

    public $rules = [
        'url'   => 'required|active_url|unique:cryptopolice_bitcointalk_topics',
    ];

    /**
     * @var array Relations
     */
    public $hasOne = [];

    public $hasMany = [];

    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];

    public $morphOne = [];

    public $morphMany = [
        'pages'      => [
            'CryptoPolice\Bitcointalk\Models\Page',
            'table' => 'cryptopolice_bitcointalk_pages',
            'name' => 'pageable'
        ]
    ];

    public $attachOne = [];
    public $attachMany = [];

    public function beforeSave()
    {
        $host = parse_url($this->attributes['url'], PHP_URL_HOST);

        if($host !== 'bitcointalk.org'){
            throw new ValidationException(['url' => $host . 'not ' . $this->host . ' !!!']);
        }

        $query_param = parse_url($this->attributes['url'], PHP_URL_QUERY);

        parse_str($query_param, $output);

        if(array_key_exists('topic', $output)) {

            $this->attributes['bitcointalk_id'] = (int)$output['topic'];

        } else {
            throw new ValidationException(['url' => 'No valid for URL for topic !!!']);
        }
    }
}
