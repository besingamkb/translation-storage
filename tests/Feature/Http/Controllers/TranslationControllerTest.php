<?php

use App\Models\User;
use App\Models\Locale;
use App\Models\TranslationKey;
use App\Models\TranslationValue;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('TranslationController', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        $this->locale = Locale::factory()->create(['code' => 'en', 'name' => 'English']);
    });

    it('can list translations', function () {
        $key = TranslationKey::factory()->create(['key' => 'greeting']);
        TranslationValue::factory()->create([
            'translation_key_id' => $key->id,
            'locale_id' => $this->locale->id,
            'value' => 'Hello',
        ]);
        $response = $this->getJson('/api/translations');
        $response->assertOk();
        $response->assertJsonFragment(['value' => 'Hello']);
    });

    it('can create a translation', function () {
        $key = TranslationKey::factory()->create(['key' => 'welcome']);
        $data = [
            'key' => 'welcome',
            'locale' => 'en',
            'value' => 'Welcome!',
        ];
        $response = $this->postJson('/api/translations', $data);
        $response->assertCreated();
        $response->assertJsonFragment(['message' => 'Translation saved successfully.']);
        $this->assertDatabaseHas('translation_values', ['value' => 'Welcome!']);
    });

    it('validates translation creation', function () {
        $response = $this->postJson('/api/translations', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['key', 'locale', 'value']);
    });

    it('can update a translation', function () {
        $key = TranslationKey::factory()->create(['key' => 'bye']);
        $translation = TranslationValue::factory()->create([
            'translation_key_id' => $key->id,
            'locale_id' => $this->locale->id,
            'value' => 'Bye',
        ]);
        $data = ['value' => 'Goodbye'];
        $response = $this->putJson("/api/translations/{$translation->id}", $data);
        $response->assertOk();
        $response->assertJsonFragment(['message' => 'Translation updated successfully.']);
        $this->assertDatabaseHas('translation_values', ['id' => $translation->id, 'value' => 'Goodbye']);
    });

    it('can delete a translation', function () {
        $key = TranslationKey::factory()->create(['key' => 'bye']);
        $translation = TranslationValue::factory()->create([
            'translation_key_id' => $key->id,
            'locale_id' => $this->locale->id,
            'value' => 'Bye',
        ]);
        $response = $this->deleteJson("/api/translations/{$translation->id}");
        $response->assertOk();
        $response->assertJsonFragment(['message' => 'Translation deleted successfully.']);
        $this->assertDatabaseMissing('translation_values', ['id' => $translation->id]);
    });

    it('can export translations', function () {
        $key = TranslationKey::factory()->create(['key' => 'exported']);
        TranslationValue::factory()->create([
            'translation_key_id' => $key->id,
            'locale_id' => $this->locale->id,
            'value' => 'Exported',
        ]);
        $response = $this->getJson('/api/translations/export?locale=en');
        $response->assertOk();
        $response->assertJsonStructure(['data', 'meta']);
    });
});
