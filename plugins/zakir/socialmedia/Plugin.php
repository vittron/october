<?php namespace Zakir\SocialMedia;

use Backend;
use System\Classes\PluginBase;

/**
 * SocialMedia Plugin Information File
 */
class Plugin extends PluginBase
{

    public $require = ['Zakir.AllFontIcons'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'zakir.socialmedia::lang.plugin.name',
            'description' => 'zakir.socialmedia::lang.plugin.description',
            'author'      => 'Zakir Hussain',
            'icon'        => 'icon-users'
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
            'Zakir\SocialMedia\Components\LinkItems' => 'LinkItems',
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
            'zakir.socialmedia.links' => [
                'tab' => 'zakir.socialmedia::lang.plugin.name',
                'label' => 'zakir.socialmedia::lang.plugin.description'
            ],
        ];
    }


    /**
     * Register setting navigation
     *
     * @return array
     */
    public function registerSettings()
    {
        return [
            'links' => [
                'label'       => 'zakir.socialmedia::lang.plugin.manage_links',
                'description' => '',
                'icon'        => 'icon-link',
                'url'         => Backend::url('zakir/socialmedia/links'),
                'category'    => 'zakir.socialmedia::lang.plugin.name',
            ]
        ];
    }
}
