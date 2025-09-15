<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // return response()->json(Comment::all());
        return response()->json('Page Not Found', 404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'comment' => 'required',
        ]);

        if (!is_numeric($request['rating'])) {
            return response()->json(array("message" => 'rating field must be number only!'), 201);
        }

        $comment = Comment::create([
            'commentor' => $request['commentor'] ? $request['commentor'] : NULL,
            'rating' => floatval($request['rating'] ? $request['rating'] : 0),
            'comment' => $request['comment']
        ]);

        return response()->json($comment, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        return response()->json('Page Not Found', 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        return response()->json('Page Not Found', 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        return response()->json('Page Not Found', 404);
    }

    public function form_store(Request $request)
    {
        $validated = $request->validate([
            'comment' => 'required',
        ]);

        if (!is_numeric($request['rating'])) {
            return response()->json(array("message" => 'rating field must be number only!'), 201);
        }

        $comment = Comment::create([
            'commentor' => $request['commentor'] ? $request['commentor'] : NULL,
            'rating' => floatval($request['rating'] ? $request['rating'] : 0),
            'comment' => $request['comment']
        ]);

        return response()->json($comment, 201);
    }

    public function all_comments()
    {
        $comments = Comment::orderBy('id', 'desc')
            ->limit(5)
            ->get();
        return response()->json($comments);
    }

    public function all_comments_count()
    {
        $commentCount = Comment::count();
        return response()->json($commentCount);
    }
}
