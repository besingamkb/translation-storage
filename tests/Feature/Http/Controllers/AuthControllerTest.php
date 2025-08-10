<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('AuthController', function () {
    it('can login with valid credentials', function () {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);
        $response->assertOk();
        $response->assertJsonStructure(['token', 'user']);
    });

    it('fails login with invalid credentials', function () {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);
        $response->assertStatus(401);
        $response->assertJsonFragment(['message' => 'Invalid credentials']);
    });

    it('fails login with missing fields', function () {
        $response = $this->postJson('/api/auth/login', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);
    });

    it('can logout when authenticated', function () {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);
        $token = $user->createToken('api-token')->plainTextToken;
        $this->withHeader('Authorization', 'Bearer ' . $token);
        $response = $this->postJson('/api/auth/logout');
        $response->assertOk();
        $response->assertJsonFragment(['message' => 'Logged out successfully.']);
    });

    it('cannot logout when not authenticated', function () {
        $response = $this->postJson('/api/auth/logout');
        $response->assertStatus(401);
        $response->assertJsonFragment(['message' => 'Unauthenticated.']);
    });
});
