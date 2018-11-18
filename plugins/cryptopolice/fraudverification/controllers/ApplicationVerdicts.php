<?php namespace CryptoPolice\FraudVerification\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class ApplicationVerdicts extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController',        'Backend\Behaviors\FormController'    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'FraudApplicationVerdicts' 
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('CryptoPolice.FraudVerification', 'FraudVerification', 'FraudApplicationVerdicts');
    }
}
