<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::paginate(2);
        return BookResource::collection($books);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'author' => 'required|string|min:3|max:100',
            'summary' => 'required|text|min:10|max:500',
            'isbn' => 'required|string|size:13|unique:books,isbn',
        ]);
        $book = Book::create($validated);

        return new BookResource($book);
    }
    public function show(Book $book)
    {
        $cachedbook = Cache::remember("book-{$book->id}", 3600, function () use ($book) {
             return $book;
        });
        return new BookResource($cachedbook);
    }
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title'   => 'required|string|min:3|max:255',
            'author'  => 'required|string|min:3|max:100',
            'summary' => 'required|string|min:10|max:500',
            'isbn'    => 'required|string|size:13|unique:books,isbn,' . $book->id,
        ]);

        $book->update($validated);
        Cache::forget("book-{$book->id}");
        return new BookResource($book);
    }

    public function destroy(Book $book)
    {
        $book->delete();
        Cache::forget("book-{$book->id}");
        return response()->noContent();
    }
}