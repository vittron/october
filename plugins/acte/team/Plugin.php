<?php namespace Acte\Team;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
      return [
        'Acte\Team\Components\Team' => 'team',
      ];
    }

    public function registerSettings()
    {
    }
}
