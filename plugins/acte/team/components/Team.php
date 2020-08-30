<?php namespace Acte\Team\Components;

use Cms\Classes\ComponentBase;
use Acte\Team\Models\Team as TeamModel;

class Team extends ComponentBase
{

  public function componentDetails()
  {
      return [
          'name' => 'Team',
          'description' => 'Display your team in cards',
      ];
  }

  public function defineProperties()
  {
    return [
      'mask' => [
        'title'             => 'Mask filter',
        'description'       => 'Apply mask to profile picture',
        'default'           => 'white-slight',
        'type'              => 'dropdown',
        'options' => [
            'blue-light' => 'blue-light',
            'red-light' => 'red-light',
            'pink-light' => 'pink-light',
            'purple-light' => 'purple-light',
            'indigo-light' => 'indigo-light',
            'cyan-light' => 'cyan-light',
            'teal-light' => 'teal-light',
            'green-light' => 'green-light',
            'lime-light' => 'lime-light',
            'yellow-light' => 'yellow-light',
            'orange-light' => 'orange-light',
            'brown-light' => 'brown-light',
            'grey-light' => 'grey-light',
            'blue-grey-light' => 'blue-grey-light',
            'black-light' => 'black-light',
            'stylish-light' => 'stylish-light',
            'white-light' => 'white-light',
            'blue-strong' => 'blue-strong',
            'red-strong' => 'red-strong',
            'pink-strong' => 'pink-strong',
            'purple-strong' => 'purple-strong',
            'indigo-strong' => 'indigo-strong',
            'cyan-strong' => 'cyan-strong',
            'teal-strong' => 'teal-strong',
            'green-strong' => 'green-strong',
            'lime-strong' => 'lime-strong',
            'yellow-strong' => 'yellow-strong',
            'orange-strong' => 'orange-strong',
            'brown-strong' => 'brown-strong',
            'grey-strong' => 'grey-strong',
            'blue-grey-strong' => 'blue-grey-strong',
            'black-strong' => 'black-strong',
            'stylish-strong' => 'stylish-strong',
            'white-strong' => 'white-strong',
            'blue-slight' => 'blue-slight',
            'red-slight' => 'red-slight',
            'pink-slight' => 'pink-slight',
            'purple-slight' => 'purple-slight',
            'indigo-slight' => 'indigo-slight',
            'cyan-slight' => 'cyan-slight',
            'teal-slight' => 'teal-slight',
            'green-slight' => 'green-slight',
            'lime-slight' => 'lime-slight',
            'yellow-slight' => 'yellow-slight',
            'orange-slight' => 'orange-slight',
            'brown-slight' => 'brown-slight',
            'grey-slight' => 'grey-slight',
            'blue-grey-slight' => 'blue-grey-slight',
            'black-slight' => 'black-slight',
            'stylish-slight' => 'stylish-slight',
            'white-slight' => 'white-slight',
        ]
      ]
    ];
  }

  public function records(){

    return TeamModel::active()->get();

  }




}
