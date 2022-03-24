<?php
namespace App\services;

use Curl\Curl;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CaptchaService{
    private $client;
    

    public function __construct(
        HttpClientInterface $client
        )
    {
        $this->client = $client;
    }

    public function captchaResponse(string $url){
        return $this->client->request('POST', $url);
    }
}