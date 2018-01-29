<?php namespace CryptoPolice\KYC\Models;

use Model;

use \October\Rain\Database\Traits\Validation;
class Settings extends Model
{
    use Validation;

    public $implement = ['System.Behaviors.SettingsModel'];

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';

    // A unique code
    public $settingsCode = 'kyc';

    public $rules = [
        'location'          => 'required',
        'subscription_key'  => 'required',
    ];

}