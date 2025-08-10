<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('UserController', function () {
    it('can list users', function () {
        $user = User::factory()->create();
        $this->actingAs($user);
        User::factory()->create(['name' => 'Alice', 'email' => 'alice@example.com']);
        User::factory()->create(['name' => 'Bob', 'email' => 'bob@example.com']);
        $response = $this->getJson('/api/users');
        $response->assertOk();
        $response->assertJsonFragment(['name' => 'Alice', 'email' => 'alice@example.com']);
        $response->assertJsonFragment(['name' => 'Bob', 'email' => 'bob@example.com']);
    });

    it('paginates users', function () {
        $user = User::factory()->create();
        $this->actingAs($user);
        User::factory(25)->create();
        $response = $this->getJson('/api/users?per_page=10');
        $response->assertOk();
        $response->assertJsonStructure(['data', 'links', 'meta']);
        $this->assertEquals(10, count($response->json('data')));
    });
});
