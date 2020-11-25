<?php 

namespace Vittron\Mallplus\Models;


use October\Rain\Database\Traits\SoftDelete;
use October\Rain\Database\Traits\Validation;
use OFFLINE\Mall\Classes\Traits\HashIds;
use OFFLINE\Mall\Models\Address as BaseAddress;
use RainLab\Location\Behaviors\LocationModel;

class Address extends BaseAddress
{
    public $rules = [
        'lines'       => 'required',
        'country_id'  => 'required|exists:rainlab_location_countries,id',
        'customer_id' => 'required|exists:offline_mall_customers,id',
        'city'        => 'required',
    ];

    public $attributes = [
        'zip' => '01001',
    ];
}
