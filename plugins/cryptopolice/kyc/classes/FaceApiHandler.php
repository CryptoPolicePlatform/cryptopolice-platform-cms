<?php namespace CryptoPolice\KYC\Classes;

use HTTP_Request2;
use CryptoPolice\KYC\Classes\Exceptions\FaceApiException;

class FaceApiHandler
{
    private $request;
    private $response;
    private $location;
    private $subscription_key;

    public function __construct($location, $subscription_key)
    {
        $this->request = new HTTP_Request2();;
        $this->location = $location;
        $this->subscription_key = $subscription_key;
    }

    public function detect($data, $parameters = null)
    {
        $endpoint = $this->getUrl() . '/face/v1.0/detect';
        return $this->execute($endpoint, $data, $parameters);
    }

    public function verify($one, $another)
    {
        $face1 = $this->detect($one);
        $face2 = $this->detect($another);

        $request_body = [
            'faceId1' => $face1[0]->faceId,
            'faceId2' => $face2[0]->faceId,
        ];

        $endpoint = $this->getUrl() . '/face/v1.0/verify';
        return $this->execute($endpoint, json_encode($request_body));
    }

    public function execute($url, $data, $parameters = null)
    {
        if($parameters) {
            $url = $url . '?' . http_build_query($parameters, '', '&');
        }

        $this->response = $this->request
                    ->setUrl($url)
                    ->setHeader([
                        'Ocp-Apim-Subscription-Key'  => $this->subscription_key,
                        'Content-Type'               => $this->getType($data) ])
                    ->setMethod(HTTP_Request2::METHOD_POST)
                    ->setBody($data)
                    ->send();

        return $this->parse();
    }

    public function getResponse()
    {
        return $this->response;
    }

    private function getUrl()
    {
        return "https://{$this->location}.api.cognitive.microsoft.com";
    }

    private function getType($data)
    {
        if(is_resource($data)) {
           return 'application/octet-stream';
        }

        if (is_string($data)) {
            return 'application/json; charset=utf-8';
        }
    }

    private function parse()
    {
        $result = json_decode($this->response->getBody(), false, 512, JSON_UNESCAPED_UNICODE);

        if(!empty($result->error)){
            throw new FaceApiException($result->error->code . ' : ' . $result->error->message);
        }

        return $result;
    }
}