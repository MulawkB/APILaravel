<?php

namespace App\Http\Controllers;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        return BookResource::collection(Book::all());
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
        return new BookResource($book);
    }
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'author'  => 'required|string|max:255',
            'summary' => 'required|string',
            'isbn'    => 'required|string|unique:books,isbn,' . $book->id,
        ]);

        $book->update($validated);

        return new BookResource($book);
    }

    public function destroy(Book $book)
    {
        $book->delete();

        return response()->json(null, 204);
    }
}