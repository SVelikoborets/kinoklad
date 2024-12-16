<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\MovieRating;
use Illuminate\Http\Request;

class MoviesController extends Controller
{
    public function index(Request $request, $page = 1)
    {
        $countries = Movie::select('country')->distinct()->pluck('country');
        $years = Movie::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');

        $query = Movie::withAvg('ratings', 'rating');

        if ($request->has('year') || $request->has('country') || $request->has('rating')) {
            $request->session()->put('filters', $request->all());
        }

        $filters = $request->session()->get('filters', []); // Получаем фильтры из сессии, или пустой массив по умолчанию

        if (isset($filters['year'])) {
            $query->where('year', $filters['year']);
        }
        if (isset($filters['country'])) {
            $query->where('country', 'like', "%{$filters['country']}%");
        }
        if (isset($filters['rating'])) {
            $query->where('rating', '>=', $filters['rating'])->latest();
        }

        $movies = $query->paginate(12, ['*'], 'page', $page);

        return view('movie.index', [
            'movies' => $movies,
            'years' => $years,
            'countries' => $countries,
            'selectedYear' => isset($filters['year']) ? $filters['year'] : '',
            'selectedCountry' => isset($filters['country']) ? $filters['country'] : '',
            'selectedRating' => isset($filters['rating']) ? $filters['rating'] : '',
        ]);
    }

    public function show($slug)
    {
        $movie = Movie::with('people', 'comments', 'ratings') ->where('slug', $slug) ->firstOrFail();
        $rating = $movie->averageRating();
        return view('movie.show', compact('movie','rating'));
    }

    public function rate(Request $request, Movie $movie)
    {
        $request->validate([
            'rating' => 'required|numeric|min:1|max:10',
        ]);

        $rating = new MovieRating([
            'user_id' => auth()->id(),
            'movie_id' => $movie->id,
            'rating' => $request->rating,
        ]);

        $rating->save();
        return back()->with('success', 'Рейтинг успешно добавлен');
    }
}
