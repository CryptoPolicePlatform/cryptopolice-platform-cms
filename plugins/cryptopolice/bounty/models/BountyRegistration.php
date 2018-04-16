<?php namespace CryptoPolice\Bounty\Models;

use Model;
use ValidationException;

/**
 * Model
 */
class BountyRegistration extends Model
{
    use \October\Rain\Database\Traits\Validation;

    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    /**
     * @var array Validation rules
     */
    public $rules = [];


    /**
     * @var array Fillable fields
     */
    protected $fillable = ['btc_status'];

    protected $jsonable = ['fields_data'];

    /**
     * @var array Relations
     */
    public $hasMany = [
        'bountyReport' => ['CryptoPolice\bounty\Models\BountyReport', 'key' => 'bounty_user_registration_id']
    ];

    public $belongsTo = [

        'user' => [
            'Rainlab\user\Models\User',
            'key' => 'user_id'
        ],

        'bounty' => [
            'CryptoPolice\bounty\Models\Bounty',
            'key' => 'bounty_campaigns_id'
        ]

    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'cryptopolice_bounty_user_registration';

    public function beforeSave()
    {

        $data = input();

        if (isset($data['BountyRegistration']) && !empty($data['BountyRegistration'])) {

            $reg = $data['BountyRegistration'];
            
            if (!$reg['approval_type']) {
                if (!$reg['reverified'] && !empty($reg['message']) || ($reg['reverified'] && empty($reg['message']))) {

                    throw new ValidationException([
                        'message' => 'Please, set Reverifed status and Message or Approval status'
                    ]);
                }
            }

            if ($reg['approval_type']) {
                if ($reg['reverified'] || !empty($reg['message'])) {
                    throw new ValidationException([
                        'message' => 'Please, remove Reverifed status and Message'
                    ]);
                }
            }
        }
    }
}