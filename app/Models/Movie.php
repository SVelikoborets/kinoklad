<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{

    protected $fillable = [
        'kinopoisk_id',
        'slug',
        'title',
        'poster_url',
        'year',
        'country',
        'description',
        'rating',
        'external_link'
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function people()
    {
        return $this->hasMany(People::class);
    }
    public function ratings()
    {
        return $this->hasMany(MovieRating::class);
    }

    public function averageRating()
    {
        return $this->ratings()->avg('rating');
    }
}
