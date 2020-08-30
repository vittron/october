<?php namespace Zakir\SocialMedia\Components;

use Cms\Classes\ComponentBase;
use Zakir\SocialMedia\Models\Link as LinkItem;

class LinkItems extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'zakir.socialmedia::lang.plugin.name',
            'description' => 'zakir.socialmedia::lang.plugin.show_links',
        ];
    }

    public function onRun()
    {
    	if(class_exists('Zakir\AllFontIcons\Models\Settings')){
	        $settings = \Zakir\AllFontIcons\Models\Settings::instance();
	        if(!empty($settings->font_awesome_link)){
		        $this->addCss($settings->font_awesome_link);
	        }
    	}
		$this->page['linkitems'] = LinkItem::get();    	
    }

}
