<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{ 
    /** 
     * Transform the resource into an array.
     * 
     *
     * 
     */
    public function toArray(Request $request): array
    {
        return [
            'title'   => $this->title,
            'author'  => strtoupper($this->author),
            'summary' => $this->summary,
            'isbn'    => $this->isbn,
            '_links' => [
                'self' => route('book.show', $this->id),
                'update' => route('book.update', $this->id),
                'delete' => route('book.destroy', $this->id),
                'all' => route('books.index'),
            ],
        ];
    }
}
