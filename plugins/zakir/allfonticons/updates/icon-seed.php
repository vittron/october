<?php namespace Zakir\AllFontIcons\Updates;

use Seeder;
use Zakir\AllFontIcons\Models\Settings;

class SeedIconsSettingModel extends Seeder
{
    public function run()
    {
		$settings = Settings::instance();
		$settings->font_awesome_link = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css';
		$settings->save();
    }
}