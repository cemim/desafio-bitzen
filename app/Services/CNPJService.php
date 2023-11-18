<?php

namespace App\Services;

use GuzzleHttp\Client;

class CNPJService
{
    protected $httpClient;

    public function __construct()
    {        
        $this->httpClient = new Client([
            'base_uri' => 'https://brasilapi.com.br/api/',            
        ]);
    }

    public function consultarCNPJ($cnpj)
    {
        try {
            $response = $this->httpClient->request('GET', "cnpj/v1/$cnpj");

            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody(), true);
            } else {
                return $response->getStatusCode();
            }
        } catch (\Exception $e) {
            return 'CNPJ Não Localizado';
        }
    }
}