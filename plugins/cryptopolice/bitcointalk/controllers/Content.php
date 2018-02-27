<?php namespace CryptoPolice\Bitcointalk\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Content Back-end Controller
 */
class Content extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('CryptoPolice.Bitcointalk', 'bitcointalk', 'content');
    }
}
