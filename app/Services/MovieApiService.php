<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use App\Models\Movie;
use App\Models\People;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MovieApiService
{
    private $apiUrl;
    private $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('services.kinopoisk.api_url') ?? throw new \InvalidArgumentException("API URL not configured");
        $this->apiKey = config('services.kinopoisk.api_key') ?? throw new \InvalidArgumentException("API Key not configured");
    }

    public function getGenres()
    {
        $genresResponse = Http::withHeaders([
            'X-API-KEY' => $this->apiKey,
        ])->get($this->apiUrl . "/api/v2.2/films/filters");

        dd($genresResponse->json());
    }
    /**
     * Fetches adventure movies from API.
     *
     * @return void
     * @throws \Exception
     */
    public function fetchFamilyMovies()
    {
        try {
            $maxRequests = 500; // Общее ограничение на количество запросов
            $requestCount = 0;

            for ($page = 1; $page <= 5; $page++) {
                if ($requestCount >= $maxRequests) {
                    break; // Прерываем цикл, если достигнут лимит запросов
                }

                $response = Http::withHeaders([
                    'X-API-KEY' => $this->apiKey,
                ])->get($this->apiUrl . '/api/v2.2/films', [
                    'genres' => 19,
                    'type' => 'FILM',
                    'page' => $page,
                ]);

                $requestCount++;

                usleep(50000);
//                dd($response->json());
                $films = $response->json()['items'];

                $filmIds = [];

                foreach ($films as $film) {
                    $filmIds[] = $film['kinopoiskId'];
                }

                foreach ($filmIds as $filmId) {
                    if ($requestCount >= $maxRequests) {
                        break; // Прерываем цикл, если достигнут лимит запросов
                    }
                    $this->fetchAndSaveMovieDetails($filmId);
                    // Обновляем счётчик запросов
                    $requestCount+=3;
                }
                Log::info('Movie fetched from page: ' . $page. 'Request count:' . $requestCount);
            }
        } catch (\Exception $e) {
            Log::error('Error fetching adventure movies: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Fetches and saves movie details.
     *
     * @param int $filmId
     * @return void
     */
    private function fetchAndSaveMovieDetails($filmId)
    {
        $movieResponse = Http::withHeaders([
            'X-API-KEY' => $this->apiKey,
        ])->get($this->apiUrl . "/api/v2.2/films/{$filmId}");

        usleep(50000);

        $movieData = $movieResponse->json();

        if($movieData['description'] !='') {

            $title = $movieData['nameRu'] ?? $movieData['nameEn'] ?? 'Фильм';
            $slug = $movieData['nameEn'] ?? $this->transliterate($movieData['nameRu']);

            $movie = Movie::updateOrCreate([
                'kinopoisk_id' => $movieData['kinopoiskId'],
            ], [
                'slug' =>Str::slug($slug),
                'title' => $title,
                'poster_url' => $movieData['posterUrl'],
                'year' => $movieData['year'] ?? 'Нет данных',
                'country' => $movieData['countries'][0]['country'] ?? 'Нет данных',
                'description' => $movieData['description'],
                'rating' => $movieData['ratingKinopoisk'],
                'external_link' => $movieData['webUrl'] ?? 'https://www.kinopoisk.ru',
            ]);

            $this->fetchAndSaveStaff($movie, $filmId);
            $this->fetchAndSavePosters($movie, $filmId);
        }
    }

    /**
     * Fetches and saves movie staff details.
     *
     * @param Movie $movie
     * @param int $filmId
     * @return void
     */

    private function fetchAndSaveStaff(Movie $movie, $filmId)
    {
        $staffResponse = Http::withHeaders([
            'X-API-KEY' => $this->apiKey,
        ])->get($this->apiUrl . "/api/v1/staff", [
            'filmId' => $filmId,
        ]);

        usleep(50000);

        $staffData = $staffResponse->json();

        foreach ($staffData as $person) {
            if (in_array($person['professionKey'], ['DIRECTOR', 'ACTOR'])) {
                People::updateOrCreate([
                    'staff_id' => $person['staffId'],
                    'movie_id' => $movie->id,
                ], [
                    'name' => trim($person['nameRu']) !== '' ? $person['nameRu'] : ($person['nameEn'] ?? 'Noname'),
                    'profession' => $person['professionKey'],
                    'poster_url' => $person['posterUrl'],
                ]);
            }
        }
    }

    /**
     * Fetches and saves movie posters.
     *
     * @param Movie $movie
     * @param int $filmId
     * @return void
     */
    private function fetchAndSavePosters(Movie $movie, $filmId)
    {
        $client = new Client();
        $response = $client->get($movie->poster_url, [
            'headers' => [
                'X-Ya-Service-Ticket' => $this->apiKey
            ]
        ]);

        usleep(50000);

        $image = $response->getBody()->getContents();

        Storage::disk('public')->put('posters/'.$filmId.'.jpg', $image);
    }

    /**
     * Transliterates Russian text to Latin script.
     *
     * @param string $text
     * @return string
     */
    private function transliterate($text)
    {
        $translit = [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
            'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
            'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
            'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'ts', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'shch', 'ы' => 'y', 'э' => 'e', 'ю' => 'yu',
            'я' => 'ya', 'ь' => '', 'ъ' => '', 'А' => 'A', 'Б' => 'B',
            'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo',
            'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I', 'Й' => 'Y', 'К' => 'K',
            'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P',
            'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F',
            'Х' => 'Kh', 'Ц' => 'Ts', 'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Shch',
            'Ы' => 'Y', 'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya'
        ];

        return strtr($text, $translit);
    }
}
