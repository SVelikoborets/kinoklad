<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MovieApiService;
use Illuminate\Support\Facades\Log;

class FetchMoviesCommand extends Command
{
    protected $signature = 'movies:fetch';
    protected $description = 'Fetch adventure movies from Kinopoisk API Unofficial';

    public function handle(MovieApiService $movieService)
    {
        try {
            Log::info('KinoKlad.Starting to fetch family movies...');
            $movieService->fetchFamilyMovies();
            Log::info('KinoKlad.Finished fetching family movies.');
        } catch (\Exception $e) {
            Log::error('KinoKlad.FetchMoviesCommand failed: ' . $e->getMessage());
        }
    }
}