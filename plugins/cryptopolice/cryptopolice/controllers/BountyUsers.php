<?php namespace CryptoPolice\CryptoPolice\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class BountyUsers extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController',        'Backend\Behaviors\FormController'    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('CryptoPolice.CryptoPolice', 'main-menu-item5', 'side-menu-item2');
    }
}
