<?php namespace Hypoflex\Sslchecker;

use Hypoflex\Sslchecker\Models\Settings;
use System\Classes\PluginBase;
use Queue;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
    }

    public function registerSettings()
    {
        return [
            'config' => [
                'label'       => 'SSL Checker',
                'icon'        => 'icon-wrench',
                'description' => 'Configure the plugin settings.',
                'class'       => 'Hypoflex\Sslchecker\Models\Settings',
//                'permissions' => ['rainlab.builder.manage_plugins'],
                'order'       => 700
            ]
        ];
    }

    /**
     * @param $schedule
     */
    public function registerSchedule($schedule)
    {
        //load the settings so we can determine, what the user set as time to reload
        //TODO: currently not working.
        //ERROR: Methode has to be string, string given
        $settings = Settings::instance();
        $time = $settings->jobRepeater;
        //register the scheduler, this will automaticly start the queue every day at 0:00
        $schedule->call(function () {
            /** Add the checker to the queue so it runs in the background, Daily */
            Queue::push('\hypoflex\sslchecker\classes\SslChecker');})
            ->daily()
            ->name('SslChecker')
            ->withoutOverlapping();
    }
}
