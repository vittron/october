<?php namespace Zakir\SocialMedia\Models;

use Model;

/**
 * Model
 */
class Link extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Sortable;
    
    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'zakir_socialmedialinks_links';

    /**
     * @var array Validation rules
     */
    public $rules = [
    	'icon' => 'required',
    	'url' => 'required|url',
    	'title' => 'required',
    ];

}
