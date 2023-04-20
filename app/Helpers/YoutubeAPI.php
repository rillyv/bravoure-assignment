<?php

namespace App\Helpers;

use Google_Client;
use Google_Service_YouTube;
use App\Helpers\WikipediaAPI;

class YoutubeAPI {
    /**
     * Fetches the most popular Youtube videos for a specific country or all countries.
     * Fetches country intro via the Wikipedia API and merges it with the filtered Youtube data.
     * Returns data in JSON format.
     */

    public function fetchPopularVideosWithCountryIntro($country, $pageToken=null) {
        // Fetches the most popular videos for a specific country.
        $client = new Google_Client();
        $client->setApplicationName('Youtube API fetch most popular videos');
        $client->setDeveloperKey(config('services.youtube.api_key'));
        $service = new Google_Service_YouTube($client);

        $queryParams = [
            'chart' => 'mostPopular',
            'regionCode' => strtoupper($country),
            'maxResults' => 50,
        ];

        if ($pageToken) {
        $queryParams['pageToken'] = $pageToken;
        }

        $response = $service->videos->listVideos('snippet,statistics', $queryParams);

        // Fetch the country intro from Wikipedia.
        $wikipedia_api = new WikipediaAPI();
        $country_intro = $wikipedia_api->fetchCountryIntro($country);

        // Filter the response data
        $filteredData = [
            'country' => $country,
            'countyIntro' => $country_intro,
            'nextPageToken' => $response->nextPageToken,
            'prevPageToken' => $response->prevPageToken,
            'items' => [],
            'pageInfo' => [
                'resultsPerPage' => $response->pageInfo->resultsPerPage,
                'totalResults' => $response->pageInfo->totalResults,
            ],
        ];

        foreach ($response->items as $item) {
            $filteredData['items'][] = [
                'country' => $country,
                'countyIntro' => $country_intro,
                'title' => $item->snippet->title,
                'description' => $item->snippet->description,
                'thumbnails' => [
                    'default' => $item->snippet->thumbnails->default->url,
                    'high' => $item->snippet->thumbnails->high->url,
                ],
            ];
        }

        return $filteredData;
    }

    public function fetchAllCountrySpecificVideos($country) {
        $nextPageToken = null;
        $allVideos = [];

        do {
            $response = $this->fetchPopularVideosWithCountryIntro($country, $nextPageToken);

            $allVideos = array_merge($allVideos, $response['items']);

            $nextPageToken = $response['nextPageToken'] ?? null;

        } while ($nextPageToken != null);

        return $allVideos;
    }

    public function fetchDataFromMultipleCountries() {
        $countries = ['gb', 'nl', 'de', 'fr', 'es', 'it', 'gr'];
        $results = [];

        foreach ($countries as $country) {
            $response = $this->fetchAllCountrySpecificVideos($country);
            $results = array_merge($results, $response);
        };

        return $results;
    }
}
