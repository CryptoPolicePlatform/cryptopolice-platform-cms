<?php namespace CryptoPolice\Academy\Components;

use ValidationException;
use Cms\Classes\ComponentBase;
use CryptoPolice\Academy\Models\Settings;

class Recaptcha extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'reCaptcha',
            'description' => 'reCaptcha invisible'
        ];
    }

    public static function verifyCaptcha()
    {

        $secret = Settings::get('recaptcha-key');
        $response = post('g-recaptcha-response');
        $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$response}");
        $captcha_success = json_decode($verify);

        if ($captcha_success->success == false) {
            throw new ValidationException([
                'recaptcha' => 'reCAPTCHA is not solved'
            ]);
        }
    }

}