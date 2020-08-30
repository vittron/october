<?php namespace Iocare\Letsencrypt;

use System\Classes\PluginBase;
use Iocare\Letsencrypt\Controllers\Settings;

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
            'name'        => 'iocare.letsencrypt::lang.plugin.name',
            'description' => 'iocare.letsencrypt::lang.plugin.description',
            'author'      => 'ioCare',
            'icon'        => 'icon-lock',
			'homepage'    => 'https://github.com/bkrajendra/oc-letsencrypt'
        ];
    }
    public function registerComponents()
    {
    }

    public function registerSettings()
    {
    }
    public function registerSchedule($schedule)
    {
        $schedule->call(function () {
            $s = new Settings;
            $s->onCreateCert(1);
        })->dailyAt('23:00');//Run everyday at 11pm
    }
}
