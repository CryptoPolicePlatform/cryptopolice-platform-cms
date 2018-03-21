<?php namespace CryptoPolice\Platform\Models;

use Model;

use \October\Rain\Database\Traits\Validation;

class Settings extends Model
{
    use Validation;

    public $implement = ['System.Behaviors.SettingsModel'];

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';

    // A unique code
    public $settingsCode = 'platform';

    public $rules = [
        'access_token' => 'required',
    ];

}