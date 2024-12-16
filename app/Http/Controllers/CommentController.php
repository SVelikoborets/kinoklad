<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Movie;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Movie $movie)
    {
        $request->validate([
            'text' => 'required|string',
        ]);

        $comment = new Comment([
            'comment' => $request->text,
            'user_id' => auth()->id(),
            'movie_id' => $movie->id,
        ]);

        $comment->save();

        return back()->with('success', 'Комментарий успешно добавлен');
    }
}
