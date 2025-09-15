<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommentController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::apiResource('comments', CommentController::class);

Route::post('/form_store', [CommentController::class, 'form_store']);
Route::get('/all_comments', [CommentController::class, 'all_comments']);
Route::get('/all_comments_count', [CommentController::class, 'all_comments_count']);
// Route::get('/hello', function () {
//     return response()->json(['message' => 'Hello from API']);
// });

// Route::fallback(function (Request $request) {
//     return response()->json([
//         'error' => 'API route not found.',
//         'requested_url' => $request->path()
//     ], 404);
// });


