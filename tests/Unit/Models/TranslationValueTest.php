<?php

use App\Models\TranslationValue;
use App\Models\TranslationKey;
use App\Models\Locale;
use App\Models\TranslationRevision;

beforeEach(function () {
    $this->translationValue = new TranslationValue();
});

describe('TranslationValue Model', function () {
    it('has correct fillable attributes', function () {
        expect($this->translationValue->getFillable())->toBe(['translation_key_id', 'locale_id', 'value']);
    });

    it('uses HasFactory trait', function () {
        expect(class_uses($this->translationValue))->toContain('Illuminate\Database\Eloquent\Factories\HasFactory');
    });

    it('can be created with fillable attributes', function () {
        $translationKey = TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test description'
        ]);

        $locale = Locale::create([
            'code' => 'en_US',
            'name' => 'English (United States)'
        ]);

        $translationValue = TranslationValue::create([
            'translation_key_id' => $translationKey->id,
            'locale_id' => $locale->id,
            'value' => 'Test value'
        ]);

        expect($translationValue->translation_key_id)->toBe($translationKey->id);
        expect($translationValue->locale_id)->toBe($locale->id);
        expect($translationValue->value)->toBe('Test value');
        expect($translationValue->id)->toBeGreaterThan(0);
    });

    it('can be updated with fillable attributes', function () {
        $translationKey = TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test description'
        ]);

        $locale = Locale::create([
            'code' => 'en_US',
            'name' => 'English (United States)'
        ]);

        $translationValue = TranslationValue::create([
            'translation_key_id' => $translationKey->id,
            'locale_id' => $locale->id,
            'value' => 'Test value'
        ]);

        $translationValue->update(['value' => 'Updated value']);

        expect($translationValue->fresh()->value)->toBe('Updated value');
    });

    it('has translationKey relationship', function () {
        expect($this->translationValue->translationKey())->toBeInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsTo');
    });

    it('has locale relationship', function () {
        expect($this->translationValue->locale())->toBeInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsTo');
    });

    it('has translationRevisions relationship', function () {
        expect($this->translationValue->translationRevisions())->toBeInstanceOf('Illuminate\Database\Eloquent\Relations\HasMany');
    });

    it('can access translationKey relationship', function () {
        $translationKey = TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test description'
        ]);

        $locale = Locale::create([
            'code' => 'en_US',
            'name' => 'English (United States)'
        ]);

        $translationValue = TranslationValue::create([
            'translation_key_id' => $translationKey->id,
            'locale_id' => $locale->id,
            'value' => 'Test value'
        ]);

        expect($translationValue->translationKey)->toBeInstanceOf(TranslationKey::class);
        expect($translationValue->translationKey->id)->toBe($translationKey->id);
    });

    it('can access locale relationship', function () {
        $translationKey = TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test description'
        ]);

        $locale = Locale::create([
            'code' => 'en_US',
            'name' => 'English (United States)'
        ]);

        $translationValue = TranslationValue::create([
            'translation_key_id' => $translationKey->id,
            'locale_id' => $locale->id,
            'value' => 'Test value'
        ]);

        expect($translationValue->locale)->toBeInstanceOf(Locale::class);
        expect($translationValue->locale->id)->toBe($locale->id);
    });

    it('can access translationRevisions relationship', function () {
        $translationKey = TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test key'
        ]);

        $locale = Locale::create([
            'code' => 'en_US',
            'name' => 'English (United States)'
        ]);

        $translationValue = TranslationValue::create([
            'translation_key_id' => $translationKey->id,
            'locale_id' => $locale->id,
            'value' => 'Test value'
        ]);

        $user = \App\Models\User::create([
            'name' => 'Test User',
            'email' => 'test' . uniqid() . '@example.com',
            'password' => 'password'
        ]);

        $revision = \App\Models\TranslationRevision::create([
            'translation_value_id' => $translationValue->id,
            'old' => 'Old value',
            'new' => 'New value',
            'user_id' => $user->id
        ]);

        expect($translationValue->translationRevisions)->toBeInstanceOf('Illuminate\Database\Eloquent\Collection');
        expect($translationValue->translationRevisions->count())->toBe(1);
    });

    it('can create translation revisions', function () {
        $translationKey = TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test key'
        ]);

        $locale = Locale::create([
            'code' => 'en_US',
            'name' => 'English (United States)'
        ]);

        $translationValue = TranslationValue::create([
            'translation_key_id' => $translationKey->id,
            'locale_id' => $locale->id,
            'value' => 'Test value'
        ]);

        $user = \App\Models\User::create([
            'name' => 'Test User',
            'email' => 'test' . uniqid() . '@example.com',
            'password' => 'password'
        ]);

        $revision = \App\Models\TranslationRevision::create([
            'translation_value_id' => $translationValue->id,
            'old' => 'Old value',
            'new' => 'New value',
            'user_id' => $user->id
        ]);

        expect($revision->translation_value_id)->toBe($translationValue->id);
        expect($revision->old)->toBe('Old value');
        expect($revision->new)->toBe('New value');
        expect($revision->user_id)->toBe($user->id);
    });

    it('has timestamps', function () {
        $translationKey = TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test description'
        ]);

        $locale = Locale::create([
            'code' => 'en_US',
            'name' => 'English (United States)'
        ]);

        $translationValue = TranslationValue::create([
            'translation_key_id' => $translationKey->id,
            'locale_id' => $locale->id,
            'value' => 'Test value'
        ]);

        expect($translationValue->created_at)->not->toBeNull();
        expect($translationValue->updated_at)->not->toBeNull();
    });

    it('can be found by translation key id', function () {
        $translationKey = TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test description'
        ]);

        $locale = Locale::create([
            'code' => 'en_US',
            'name' => 'English (United States)'
        ]);

        TranslationValue::create([
            'translation_key_id' => $translationKey->id,
            'locale_id' => $locale->id,
            'value' => 'Test value'
        ]);

        $foundValues = TranslationValue::where('translation_key_id', $translationKey->id)->get();

        expect($foundValues->count())->toBeGreaterThan(0);
        expect($foundValues->first()->translation_key_id)->toBe($translationKey->id);
    });

    it('can be found by locale id', function () {
        $translationKey = TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test description'
        ]);

        $locale = Locale::create([
            'code' => 'en_US',
            'name' => 'English (United States)'
        ]);

        TranslationValue::create([
            'translation_key_id' => $translationKey->id,
            'locale_id' => $locale->id,
            'value' => 'Test value'
        ]);

        $foundValues = TranslationValue::where('locale_id', $locale->id)->get();

        expect($foundValues->count())->toBeGreaterThan(0);
        expect($foundValues->first()->locale_id)->toBe($locale->id);
    });

    it('can be found by value', function () {
        $translationKey = TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test description'
        ]);

        $locale = Locale::create([
            'code' => 'en_US',
            'name' => 'English (United States)'
        ]);

        TranslationValue::create([
            'translation_key_id' => $translationKey->id,
            'locale_id' => $locale->id,
            'value' => 'Test value'
        ]);

        $foundValues = TranslationValue::where('value', 'Test value')->get();

        expect($foundValues->count())->toBeGreaterThan(0);
        expect($foundValues->first()->value)->toBe('Test value');
    });

    it('can be deleted', function () {
        $translationKey = TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test description'
        ]);

        $locale = Locale::create([
            'code' => 'en_US',
            'name' => 'English (United States)'
        ]);

        $translationValue = TranslationValue::create([
            'translation_key_id' => $translationKey->id,
            'locale_id' => $locale->id,
            'value' => 'Test value'
        ]);

        $valueId = $translationValue->id;
        $translationValue->delete();

        expect(TranslationValue::find($valueId))->toBeNull();
    });

    it('can be mass assigned', function () {
        $translationKey = TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test description'
        ]);

        $locale = Locale::create([
            'code' => 'en_US',
            'name' => 'English (United States)'
        ]);

        $valueData = [
            'translation_key_id' => $translationKey->id,
            'locale_id' => $locale->id,
            'value' => 'Another value'
        ];

        $translationValue = TranslationValue::create($valueData);

        expect($translationValue->translation_key_id)->toBe($translationKey->id);
        expect($translationValue->locale_id)->toBe($locale->id);
        expect($translationValue->value)->toBe('Another value');
    });

    it('can be updated via mass assignment', function () {
        $translationKey = TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test description'
        ]);

        $locale = Locale::create([
            'code' => 'en_US',
            'name' => 'English (United States)'
        ]);

        $translationValue = TranslationValue::create([
            'translation_key_id' => $translationKey->id,
            'locale_id' => $locale->id,
            'value' => 'Test value'
        ]);

        $translationValue->fill(['value' => 'Updated value']);
        $translationValue->save();

        expect($translationValue->fresh()->value)->toBe('Updated value');
    });

    it('can handle long text values', function () {
        $translationKey = TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test description'
        ]);

        $locale = Locale::create([
            'code' => 'en_US',
            'name' => 'English (United States)'
        ]);

        $longValue = str_repeat('This is a very long translation value that should be handled properly. ', 10);

        $translationValue = TranslationValue::create([
            'translation_key_id' => $translationKey->id,
            'locale_id' => $locale->id,
            'value' => $longValue
        ]);

        expect($translationValue->value)->toBe($longValue);
        expect(strlen($translationValue->value))->toBeGreaterThan(500);
    });

    it('can be found by partial value match', function () {
        $translationKey = TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test description'
        ]);

        $locale = Locale::create([
            'code' => 'en_US',
            'name' => 'English (United States)'
        ]);

        TranslationValue::create([
            'translation_key_id' => $translationKey->id,
            'locale_id' => $locale->id,
            'value' => 'Test value for search'
        ]);

        $foundValues = TranslationValue::where('value', 'like', '%search%')->get();

        expect($foundValues->count())->toBeGreaterThan(0);
        expect($foundValues->first()->value)->toContain('search');
    });
}); 