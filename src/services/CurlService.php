<?php
namespace App\services;



class CurlService{

    public function curlManager(string $url){

        $curl = \curl_init($url);

        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        return curl_exec($curl);
    }
}