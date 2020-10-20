<?php namespace Zakir\AllFontIcons\Components;

use Cms\Classes\ComponentBase;
use Zakir\AllFontIcons\Models\Settings;

class FontIconsCSS extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'FontIconsCSS Component',
            'description' => 'This component will add Font-awesome CSS link so your icon will be visible'
        ];
    }

    public function onRun()
    {
        $settings = Settings::instance();
        if(!empty($settings->font_awesome_link)){
	        $this->addCss($settings->font_awesome_link);
        }
    }
}
