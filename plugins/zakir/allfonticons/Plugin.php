<?php namespace Zakir\AllFontIcons;

use Backend;
use System\Classes\PluginBase;

/**
 * AllFontIcons Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'zakir.allfonticons::lang.plugin.name',
            'description' => 'Backend formwidget for all font awesome icons starting from 4.0.0 version',
            'author'      => 'Zakir Hussain',
            'icon'        => 'icon-font-awesome'
        ];
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            'Zakir\AllFontIcons\Components\FontIconsCSS' => 'FontIconsCSS',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'zakir.allfonticons.fotnawesomeurl' => [
                'tab' => 'zakir.allfonticons::lang.plugin.name',
                'label' => 'Permission to change Font awesome url'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerSettings()
    {
        return [
            'setting' => [
                'label'       => 'zakir.allfonticons::lang.icon.name',
                'description' => 'zakir.allfonticons::lang.icon.description',
                'category'    => 'zakir.allfonticons::lang.plugin.name',
	            'icon'        => 'icon-url',
	            'class'       => 'Zakir\AllFontIcons\Models\Settings',
                'permissions' => ['zakir.allfonticons.*'],
                'order'       => 500,
            ]
        ];
    }

	public function registerFormWidgets()
	{
	    return [
	        '\Zakir\AllFontIcons\FormWidgets\Icon' => 'zakir_allfonticons',
	    ];
	}
}
