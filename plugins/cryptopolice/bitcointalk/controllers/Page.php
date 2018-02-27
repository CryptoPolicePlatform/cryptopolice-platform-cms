<?php namespace CryptoPolice\Bitcointalk\Controllers;

use BackendMenu;
use Backend\Classes\Controller;


use CryptoPolice\Bitcointalk\Models\Page as Model;
use CryptoPolice\Bitcointalk\Classes\Content;

/**
 * Page Back-end Controller
 */
class Page extends Controller
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

        BackendMenu::setContext('CryptoPolice.Bitcointalk', 'bitcointalk', 'page');
    }

    public function show()
    {
        list($id) = $this->params;

        $model = Model::findOrFail($id);

        $this->pageTitle =  $model->title;

        $this->vars['model'] = $model;

        return $this->makePartial('show');
    }

}
