<?php namespace Zakir\AllFontIcons\Models;

use Lang;
use Model;
use ValidationException;

class Settings extends Model 
{

	use \October\Rain\Database\Traits\Validation;
	use \Zakir\AllFontIcons\Traits\Utility;

    public $implement = ['System.Behaviors.SettingsModel'];
    public $settingsCode = 'AllFontIcons_settings';
    public $settingsFields = 'fields.yaml';

    public $rules = [
    	'font_awesome_link' => 'required'
    ];

    public function beforeSave(){
    	$this->extractFontAwesome();
    }

    /**
     * Extract Font Awesome URL icons attributes and save it into database
     *
     * @throws  ValidationException
     */
    private function extractFontAwesome(){
		$url = $this->value['font_awesome_link'];

		$parsed_file = $this->parseCssFile($url);
		preg_match_all("/fa\-([\w-]+):before/", $parsed_file, $matches);
		if(!$matches || !$matches[0]){
			throw new ValidationException(['font_awesome_link'=>Lang::get('zakir.allfonticons::lang.icon.invalid_fa_icon_file')]);
		}
		
		//Check Font Awesome version:  
		preg_match("/Font Awesome Free (.*) by @fontawesome/", $parsed_file, $version);
		if($version && (intval(trim($version[1])) >= 5)){
			$font_awesome_icons = $this->getFontAwesome5($matches,trim($version[1]));
		}else{
			$font_awesome_icons = $this->getFontAwesomeLessThen5($matches);
		}

		$this->value = array_merge($this->value,["font_awesome_icons"=>json_encode($font_awesome_icons)]);
    }

    private function getFontAwesome5($matches=[],$version='')
    {
    	$font_awesome_icons = [];
    	$brands_arr = $this->getFA5RenamedBrandIcons();
    	$regular_arr = $this->getFA5RenamedRegularIcons();
    	$renamed_arr = array_merge($regular_arr,$brands_arr);
		foreach($matches[0] as $value){
			$icon_title = str_replace(':before','',$value);
			if(array_key_exists($icon_title,$renamed_arr)){
				$font_awesome_icons[] = $renamed_arr[$icon_title]." ".$icon_title;
			}else{
				$font_awesome_icons[] = "fa ".$icon_title;
			}
		}
		return $font_awesome_icons;
    }

    private function getFontAwesomeLessThen5($matches=[])
    {
    	$font_awesome_icons = [];
		foreach ($matches[0] as $value){
			$font_awesome_icons[] = 'fa '.str_replace(':before','',$value);
		}
		return $font_awesome_icons;
    }

}