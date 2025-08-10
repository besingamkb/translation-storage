<?php

use App\Models\Locale;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('LocaleController', function () {
    it('can list locales', function () {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);
        Locale::factory()->create(['code' => 'en', 'name' => 'English']);
        Locale::factory()->create(['code' => 'fr', 'name' => 'French']);
        $response = $this->getJson('/api/locales');
        $response->assertOk();
        $response->assertJsonFragment(['code' => 'en', 'name' => 'English']);
        $response->assertJsonFragment(['code' => 'fr', 'name' => 'French']);
    });

    it('can show a locale', function () {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);
        $locale = Locale::factory()->create(['code' => 'en', 'name' => 'English']);
        $response = $this->getJson("/api/locales/{$locale->id}");
        $response->assertOk();
        $response->assertJsonFragment(['code' => 'en', 'name' => 'English']);
    });

    it('returns 404 for missing locale', function () {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);
        $response = $this->getJson('/api/locales/999');
        $response->assertNotFound();
    });

    it('can create a locale', function () {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);
        $data = ['code' => 'es', 'name' => 'Spanish'];
        $response = $this->postJson('/api/locales', $data);
        $response->assertCreated();
        $response->assertJsonFragment(['code' => 'es', 'name' => 'Spanish']);
        $this->assertDatabaseHas('locales', ['code' => 'es', 'name' => 'Spanish']);
    });

    it('validates locale creation', function () {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);
        $response = $this->postJson('/api/locales', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['code', 'name']);
    });

    it('can update a locale', function () {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);
        $locale = Locale::factory()->create(['code' => 'en', 'name' => 'English']);
        $data = ['name' => 'Updated English'];
        $response = $this->putJson("/api/locales/{$locale->id}", $data);
        $response->assertOk();
        $response->assertJsonFragment(['name' => 'Updated English']);
        $this->assertDatabaseHas('locales', ['id' => $locale->id, 'name' => 'Updated English']);
    });

    it('returns 404 for update on missing locale', function () {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);
        $response = $this->putJson('/api/locales/999', ['name' => 'Test']);
        $response->assertNotFound();
    });

    it('can delete a locale', function () {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);
        $locale = Locale::factory()->create(['code' => 'en', 'name' => 'English']);
        $response = $this->deleteJson("/api/locales/{$locale->id}");
        $response->assertOk();
        $response->assertJsonFragment(['message' => 'Locale deleted successfully']);
        $this->assertDatabaseMissing('locales', ['id' => $locale->id]);
    });

    it('returns 404 for delete on missing locale', function () {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);
        $response = $this->deleteJson('/api/locales/999');
        $response->assertNotFound();
    });
});
