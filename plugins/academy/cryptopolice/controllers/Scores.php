<?php namespace Academy\CryptoPolice\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Scores extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController','Backend\Behaviors\FormController'];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
//    public $reorderConfig = 'config_reorder.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Academy.CryptoPolice', 'main-menu-item2');
    }
}
