<?php namespace CryptoPolice\KYC\Components;

use Cms\Classes\ComponentBase;
use October\Rain\Support\Facades\Flash;

use HTTP_Request2;

use Validator, Storage, Auth, Event, Log, Exception;
use October\Rain\Exception\ValidationException;

use CryptoPolice\KYC\Classes\FaceApiHandler;
use CryptoPolice\KYC\Models\Settings;


class KYC extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'KYC Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function onRun()
    {

    }

    private function getFaceApiHandler()
    {
        return new FaceApiHandler(Settings::get('location'), Settings::get('subscription_key'));
    }

    public function defineProperties()
    {
        return [];
    }

    public function onVerification()
    {
        $data = input();

        if($this->validate($data)) {

            $files = $data['files'];

            $fp1 = fopen($files['selfie_with_document']->getRealPath(), 'rb');
            $fp2 = fopen($files['doc_front_side']->getRealPath(), 'rb');
            $fp3 = fopen($files['doc_back_side']->getRealPath(), 'rb');

            try {

                $verify = $this->getFaceApiHandler()->verify($fp1, $fp2);

                Event::fire('cryptopolice.kyc.verify', [&$verify]);

                $success = $this->saveToS3([
                    'selfie_with_document'  => $fp1,
                    'doc_front_side'        => $fp2,
                    'doc_back_side'         => $fp3,
                    ]);

                fclose($fp1);
                fclose($fp2);
                fclose($fp3);

                return $success;
                
            }   catch (Exception $e) {

                Log::error($e);

                Flash::error('Oops! Something went wrong! Try again or contact support!');
            }
        }
    }

    private function  saveToS3(array $files)
    {
        $disk = Storage::disk('s3');
        $user = Auth::getUser();

        $count = 0;

        foreach ($files as $name => $file){

            $path = 'kyc-files/'.$user->id .'/' . date("Y-m-d H:i:s") . '/' . $name . '.jpg';
            
            if($result = $disk->put($path, $file)){
                $count++;
            }
        }

        return (int)$count === (int)count($files);
    }

    private function validate($data)
    {
        $rules =     [
            'files.selfie_with_document'  => 'required|image|mimes:jpg,jpeg|max:5000',
            'files.doc_front_side'        => 'required|image|mimes:jpg,jpeg|max:5000',
            'files.doc_back_side'         => 'required|image|mimes:jpg,jpeg|max:5000',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            $message = implode('<br/>', $validator->messages()->all());
            Flash::error($message);
        } else {
            return true;
        }
    }
}
