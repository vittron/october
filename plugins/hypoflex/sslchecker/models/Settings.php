<?php namespace Hypoflex\Sslchecker\Models;

use Model;

class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'hypoflex_sslchecker_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';
}