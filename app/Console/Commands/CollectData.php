<?php

namespace App\Console\Commands;

use App\Helpers\YoutubeAPI;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CollectData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:collect-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collects and caches all the data.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cacheKey = 'data';

        $data = Cache::rememberForever($cacheKey, function () {
            $youtubeAPI = new YoutubeAPI();
            return $youtubeAPI->fetchDataFromMultipleCountries();
        });
    }
}
