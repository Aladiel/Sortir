<?php

namespace App\Service;



use Symfony\Contracts\HttpClient\HttpClientInterface;

class CallApiService {

    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this -> client = $client;
    }

    public function getVillesData(): array {
        $response = $this -> client -> request(
            'GET',
            'https://geo.api.gouv.fr/communes?nom=&fields=departement&boost=population&limit=5'
        );
        return $response -> toArray();
    }
}