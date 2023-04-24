<?php

namespace App\Http\Controllers;


use App\Helpers\YoutubeAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;


class ApiController extends Controller
{
    public function fetchData(Request $request) {
        $country = $request->query('country', null);
        $allVideos = collect(Cache::get('data'));

        if (isset($country)) {
            $allVideos = $allVideos->filter(fn ($c) => $c['country'] === $country);
        }

        $perPage = 50;
        $offset = $request->query('offset', 0);
        $page = $request->query('page', 1);
        $startIndex = ($page - 1) * $perPage + $offset;

        // Slice the collection using the offset and limit (perPage)
        $slicedVideos = $allVideos->slice($startIndex, $perPage)->values();

        // Create a LengthAwarePaginator instance using the sliced collection
        $videosPaginator = new LengthAwarePaginator($slicedVideos, $allVideos->count(), $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return response()->json($videosPaginator);
    }

    public function test() {
        $youtubeAPI = new YoutubeAPI();

        return $youtubeAPI->fetchPopularVideosWithCountryIntro($country='nl');
    }
}