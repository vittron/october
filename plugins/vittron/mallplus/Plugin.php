<?php

namespace Vittron\Mallplus;

use Illuminate\Support\Facades\Event;
use October\Rain\Support\Facades\Config;
use System\Classes\PluginBase;
use Vittron\Mallplus\Classes\SignUpHandler as MySignUpHandler;
use OFFLINE\Mall\Classes\Customer\SignUpHandler;
use Vittron\Mallplus\Classes\EventHandler;

class Plugin extends PluginBase
{
    public $require = ['OFFLINE.Mall'];

    public function pluginDetails()
    {
        return [
            'name' => 'Mallplus plugin',
            'description' => 'Provides custom enhancements and fixes to OFFLINE.Mall plugin',
            'author' => 'Vitalii Tron',
            'icon' => 'icon-leaf',
        ];
    }

    public function boot()
    {
        $this->registerEvents();
    }

    public function register()
    {
        $this->app->bind(SignUpHandler::class, function () {
            return new MySignUpHandler();
        });
    }

    public function registerComponents()
    {
        return [
            'Vittron\Mallplus\Components\SignUp' => 'signUpPlus',
            'Vittron\Mallplus\Components\Checkout' => 'checkoutPlus',
        ];
    }

    public function registerMailTemplates()
    {
        return [
            'vittron.mallplus::checkout.succeeded',
            'vittron.mallplus::admin.checkout_succeeded',
        ];
    }

    public function registerMailPartials()
    {
        return [
            'mallplus.order.payment_state' => 'vittron.mallplus::_partials.order.payment_state',
            'mallplus.order.table' => 'vittron.mallplus::_partials.order.table',
            'mallplus.order.addresses' => 'vittron.mallplus::_partials.order.addresses',
            'mallplus.customer.address' => 'vittron.mallplus::_partials.customer.address',
        ];
    }

    protected function registerEvents()
    {
        Event::subscribe(EventHandler::class);
    }

    public function registerMarkupTags()
    {
        return [
            'functions' => [
                'config_get' => function($key, $default = null) {
                    return Config::get($key, $default);
                },
            ]
        ];
    }
}