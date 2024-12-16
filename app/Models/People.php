<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class People extends Model
{
    protected $fillable = ['staff_id', 'movie_id', 'name', 'profession', 'poster_url'];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
