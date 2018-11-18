<?php namespace CryptoPolice\FraudVerification\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class ApplicationTypes extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController',        'Backend\Behaviors\FormController'    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'FraudApplicationTypes' 
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('CryptoPolice.FraudVerification', 'FraudVerification', 'FraudApplicationTypes');
    }
}
