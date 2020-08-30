<?php namespace Iocare\Letsencrypt\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Letsencrypts extends Controller
{
    public $implement = ['Backend\Behaviors\FormController'];
    
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Iocare.Letsencrypt', 'main-menu-item');
    }
}