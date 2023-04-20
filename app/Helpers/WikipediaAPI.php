<?php

namespace App\Helpers;
use GuzzleHttp\Client;

class WikipediaAPI {
    /**
     * Fetches country intro for a specific country.
     * Returns the data as a string?/json?.
     */
    public function fetchCountryIntro($country) {
        $country_codes = [
            'gb' => 'United_Kingdom',
            'nl' => 'Netherlands',
            'de' => 'Germany',
            'fr' => 'France',
            'es' => 'Spain',
            'it' => 'Italy',
            'gr' => 'Greece',
        ];

        $client = new Client();
        $response = $client->get('https://en.wikipedia.org/w/api.php', [
        'query' => [
            'action' => 'query',
            'format' => 'json',
            'prop' => 'extracts',
            'titles' => $country_codes[$country],
            'exintro' => 1,
            'explaintext' => 1
        ]
        ]);

        return $response;
        // $data = json_decode($response->getBody(), true);
        // $initialParagraph = current($data['query']['pages'])['extract'];

        // return $initialParagraph;
    }
}
