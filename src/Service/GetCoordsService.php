<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GetCoordsService
{
    public const URL = 'https://geo.api.gouv.fr/communes';


    public function __construct(private HttpClientInterface $client)
    {
    }


    /**
     * @return float[]
     */
    public function getCenterOfCity(string $city): array
    {
        $response = $this->client->request(
            'GET',
            self::URL,
            [
                'query' => [
                    'nom' => $city,
                    'fields' => 'centre',
                    'limit' => 1
                ]
            ]
        );

        $city = json_decode($response->getContent(), true);

        // Latitude and longitude are inversed in the API return
        return [
            $city[0]['centre']['coordinates'][1],
            $city[0]['centre']['coordinates'][0]
        ];
    }
}
