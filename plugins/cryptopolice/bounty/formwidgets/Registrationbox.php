<?php namespace Cryptopolice\Bounty\FormWidgets;

use Cryptopolice\Bounty\Models\BountyRegistration;
use Backend\Classes\FormWidgetBase;
use Config;

class RegistrationBox extends FormWidgetBase
{
    public function widgetDetails()
    {
        return [
            'name' => 'Registrationbox',
            'description' => 'Field for registration data'
        ];
    }

    public function render(){
        $this->prepareVars();
        return $this->makePartial('widget');
    }

    public function prepareVars() {
        $this->vars['registration_id'] = $this->model->bounty_user_registration_id;
        $this->vars['registration_data'] = BountyRegistration::where('id', $this->model->bounty_user_registration_id)->value('fields_data');
    }
}
