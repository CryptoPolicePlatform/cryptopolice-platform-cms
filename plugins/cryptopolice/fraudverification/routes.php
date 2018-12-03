<?php
header("Content-Security-Policy: default-src 'self' data: https://www.googletagmanager.com https://www.google-analytics.com https://mc.yandex.ru https://www.google.com https://www.gstatic.com https://ajax.googleapis.com https://www.google.com/recaptcha/api.js 'unsafe-inline' 'unsafe-eval'; base-uri 'self'; block-all-mixed-content; font-src 'self' https://maxcdn.bootstrapcdn.com https://fonts.gstatic.com data: fonts.gstatic.com; style-src 'self' https://maxcdn.bootstrapcdn.com https://fonts.googleapis.com 'unsafe-inline' 'unsafe-eval';connect-src 'self' https://mc.yandex.ru https://cdn.jsdelivr.net, script-src 'self' https://mc.yandex.ru https://www.google.com/recaptcha/api.js https://www.google-analytics.com https://www.google.com https://www.gstatic.com https://fonts.googleapis.com https://www.googletagmanager.com https://ajax.googleapis.com 'unsafe-inline' 'unsafe-eval'; frame-src 'self' https://www.google.com https://www.google.com/recaptcha/api.js");

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use CryptoPolice\FraudVerification\Components\Officer as Officer;
use CryptoPolice\Platform\Models\Settings;


Route::post('/api/submit-fraud-application', function () {

    $settings = Settings::instance();

   if (array_search(request()->getClientIp(), array_column($settings->white_list, 'ip')) !== false) {

       if (get('access_token') == $settings->access_token) {

           try {

               $result = Officer::onSubmitFraudApplication(2,post('domain'),post('task'),post('application_type_id'));
               return $result;

           } catch (\Exception $e) {

               Log::error($e->getMessage());
               return response(error('Something went wrong!').$e->getMessage());
           }


        } else {

            trace_log("Access failed, [/api/submit-fraud-application] - form " . request()->getClientIp());
            return response(error('Authorization failed, invalid token!'));
        }
    } else {

        trace_log("Access failed, [/api/submit-fraud-application] - " . request()->getClientIp());
        return response(error('Add your IP to White list!'));
    }


});


Route::get('/api/get-fraud-application-types', function () {

    $settings = Settings::instance();

    if (array_search(request()->getClientIp(), array_column($settings->white_list, 'ip')) !== false) {

        if (get('access_token') == $settings->access_token) {

            try {

                $result = Officer::GetApplicationTypes();
                return response()->json($result,200);

            } catch (\Exception $e) {

                Log::error($e->getMessage());
                return response(error('Something went wrong while getting data! ').$e->getMessage());
            }


        } else {

            trace_log("Access failed, [/api/submit-fraud-application] - form " . request()->getClientIp());
            return response(error('Authorization failed, invalid token'));
        }
    } else {

        trace_log("Access failed, [/api/submit-fraud-application] - " . request()->getClientIp());
        return response(error('Add your IP to White list!'));
    }


});


?>