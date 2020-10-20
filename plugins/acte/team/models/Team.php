<?php namespace Acte\Team\Models;

use Model;


/**
 * Model
 */
class Team extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Sortable;

    public $implement = ['@RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = ['position','description'];

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;

    public $jsonable = [ 'socials' ];

    /** Relations **/
    public $attachOne = [
      'img' => ['System\Models\File', 'delete' => true],
    ];

    public $rules = [
      'is_active' => 'boolean',
      'name' => 'required|string|max:64',
      'position' => 'required|string|max:64',
      'description' => 'nullable|string|max:2048',
    ];

    public $table = 'acte_team_team';

    /** Scopes **/
    public function scopeActive($query){
      return $query->where('is_active', 1);
    }



    public function beforeSave()
    {

      /** used for mdbootstrap integration **/
      /** add btn key to socials **/

      $socials = array();

      foreach ($this->socials as $key => $item) {

        $btn = [
          'facebook' => 'fb',
          'twitter' => 'tw',
          'google-plus' => 'gplus',
          'linkedin' => 'li',
          'instagram' => 'ins',
          'pinterest' => 'pin',
          'youtube' => 'yt',
          'dribble' => 'dribble',
          'vk' => 'vk',
          'stack-overflow' => 'so',
          'github' => 'git',
          'envelope' => 'email',
          'reddit-alien' => 'reddit'
        ];


        $social = $item['social'];
        $item['btn'] = $btn[$social];
        array_push($socials, $item);

      }

      $this->socials = $socials;


    }


}
