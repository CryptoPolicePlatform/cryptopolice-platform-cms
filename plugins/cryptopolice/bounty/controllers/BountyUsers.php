<?php namespace CryptoPolice\Bounty\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class BountyReports extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController'
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('CryptoPolice.Bounty', 'bounty-campaign', 'users-bounties');
    }
}
