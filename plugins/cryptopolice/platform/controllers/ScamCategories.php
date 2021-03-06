<?php namespace CryptoPolice\Platform\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class ScamCategories extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController',        'Backend\Behaviors\FormController'    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('CryptoPolice.Platform', 'scams', 'side-menu-item');
    }
}
