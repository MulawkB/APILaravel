<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_uses_professional_email(): void
    {
        $user = new \App\Models\User([ 
            'email' => 'john@entreprise.com '
        ]);

        $this->assertTrue($user->usesProfessionalEmail());
    }
    public function test_uses_non_professional_email(): void
    {
        $user = new \App\Models\User([ 
            'email' => 'john@gmail.com'
        ]);
        $this->assertFalse($user->usesProfessionalEmail());
    } 
}
