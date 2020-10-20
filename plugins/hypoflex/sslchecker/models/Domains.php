<?php namespace Hypoflex\Sslchecker\Models;

use Model;

/**
 * Model
 */
class Domains extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];
    protected $fillable = ['active'];


    /**
     * @var string The database table used by the model.
     */
    public $table = 'hypoflex_sslchecker_domains';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
