<?php 
namespace CryptoPolice\Bitcointalk\Models;

use Model;

/**
 * Settings Model
 */
class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'bitcointalk_settings';

    public $settingsFields = 'fields.yaml';

    public function getActiveAttribute($value)
    {
        return $value === null ? 1 : $value;
    }
}