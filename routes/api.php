<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\BookController;
use App\Http\Controllers\API\UserController;

Route::get('/ping', function () {
return response()->json([
'message' => 'pong',
]);

});
Route::post('/register', [UserController::class, 'register']);
Route::middleware('throttle:10,1')->group(function () {
    Route::post('/login', [UserController::class, 'login']);
});
Route::middleware('auth:sanctum')->post('/logout', [UserController::class, 'logout']);

Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{book}', [BookController::class, 'show'])->name('book.show');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/books', [BookController::class, 'store'])->name('book.store');
    Route::match(['PUT', 'PATCH'], '/books/{book}', [BookController::class, 'update'])->name('book.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('book.destroy');
});