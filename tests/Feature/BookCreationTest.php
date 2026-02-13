<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class BookCreationTest extends TestCase
{
    use RefreshDatabase;
    public function test_book_creation()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $bookData = [
            'title' => 'Test book',
            'author' => 'john doe',
            'summary' => 'ceci est un test de l\'api',
            'isbn' => '1234567890123',
        ];

        $response = $this->postJson('/api/v1/books', $bookData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('books',[
            'title' => 'Test book',
            'author' => 'john doe',
            'summary' => 'ceci est un test de l\'api',
            'isbn' => '1234567890123',
        ]);
    }
    public function test_book_creation_validation()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $bookData = [
            'title' => 'Te',
            'author' => 'jo',
            'summary' => 'test',
            'isbn' => '123456789012',
        ];

        $response = $this->postJson('/api/v1/books', $bookData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'author', 'summary', 'isbn']);
        $this->assertDatabaseMissing('books', [
        'isbn' => '1234567890124',
        ]);
    }
    public function test_book_creation_unauthenticated()
    {
        $bookData = [
            'title' => 'Test book',
            'author' => 'john doe',
            'summary' => 'ceci est un test de l\'api',
            'isbn' => '1234567890123',
        ];

        $response = $this->postJson('/api/v1/books', $bookData);

        $response->assertStatus(401);
        $this->assertDatabaseMissing('books', [
        'isbn' => '1234567890124',
        ]);
    }
}