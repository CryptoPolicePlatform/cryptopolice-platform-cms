<?php namespace cryptopolice\airdrop\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Airdrops extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController',        'Backend\Behaviors\FormController'    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('cryptopolice.airdrop', 'main-menu-item');
    }
}
