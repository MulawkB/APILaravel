<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use OpenApi\Attributes as OA;

class BookController extends Controller
{
    #[OA\Get(
        path: "/books",
        summary: "Liste des livres",
        description: "Récupère une liste paginée de livres.",
        tags: ["Books"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Liste des livres récupérée avec succès",
            )
        ]
    )]
    public function index()
    {
        $books = Book::paginate(2);
        return BookResource::collection($books);
    }
    #[OA\Post(
        path: "/books",
        summary: "Créer un livre",
        description: "Crée un nouveau livre.",
        tags: ["Books"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/BookRequest")
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Livre créé avec succès",
            ),
            new OA\Response(
                response: 422,
                description: "Erreur de validation",
            )
        ]
    )]
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
    #[OA\Get(
        path: "/books/{book}",
        summary: "Récupérer un livre",
        description: "Récupère un livre spécifique.",
        tags: ["Books"],
        parameters: [
            new OA\Parameter(
                name: "book",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Livre récupéré avec succès",
            ),
            new OA\Response(
                response: 404,
                description: "Livre non trouvé",
            )
        ]
    )]
    public function show(Book $book)
    {
        $cachedbook = Cache::remember("book-{$book->id}", 3600, function () use ($book) {
            return $book;
        });
        return new BookResource($cachedbook);
    }
    #[OA\Put(
        path: "/books/{book}",
        summary: "Mettre à jour un livre",
        description: "Met à jour un livre existant.",
        tags: ["Books"],
        parameters: [
            new OA\Parameter(
                name: "book",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/BookRequest")
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Livre mis à jour avec succès",
            ),
            new OA\Response(
                response: 422,
                description: "Erreur de validation",
            )
        ]
    )]
    
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
    #[OA\Delete(
        path: "/books/{book}",
        summary: "Supprimer un livre",
        description: "Supprime un livre existant.",
        tags: ["Books"],
        parameters: [
            new OA\Parameter(
                name: "book",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: "Livre supprimé avec succès",
            ),
            new OA\Response(
                response: 404,
                description: "Livre non trouvé",
            )
        ]
    )]
    #[OA\HeaderParameter(name: "Accept", required: true, schema: new OA\Schema(type: "string", example: "application/json"))]
    #[OA\HeaderParameter(name: "Authorization", required: true, schema: new OA\Schema(type: "string", example: "Bearer 1|123....token...aBC"))]
    public function destroy(Book $book)
    {
        $book->delete();
        Cache::forget("book-{$book->id}");
        return response()->noContent();
    }
}