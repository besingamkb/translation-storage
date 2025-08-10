<?php

use App\Models\Locale;
use App\Models\TranslationKey;
use App\Models\TranslationValue;
use App\Models\TranslationRevision;

beforeEach(function () {
    $this->translationRevision = new TranslationRevision();
});

describe('TranslationRevision Model', function () {
    it('has correct fillable attributes', function () {
        expect($this->translationRevision->getFillable())->toBe([
            'translation_value_id',
            'old',
            'new',
            'user_id'
        ]);
    });

    it('uses HasFactory trait', function () {
        expect(class_uses($this->translationRevision))->toContain('Illuminate\Database\Eloquent\Factories\HasFactory');
    });

    it('can be created with fillable attributes', function () {
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

        $revision = TranslationRevision::create([
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

    it('can be updated with fillable attributes', function () {
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

        $revision = TranslationRevision::create([
            'translation_value_id' => $translationValue->id,
            'old' => 'Old value',
            'new' => 'New value',
            'user_id' => $user->id
        ]);

        $revision->update(['new' => 'Updated value']);

        expect($revision->fresh()->new)->toBe('Updated value');
    });

    it('has translationValue relationship', function () {
        expect($this->translationRevision->translationValue())->toBeInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsTo');
    });

    it('can access translationValue relationship', function () {
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

        $revision = TranslationRevision::create([
            'translation_value_id' => $translationValue->id,
            'old' => 'Old value',
            'new' => 'New value',
            'user_id' => $user->id
        ]);

        expect($revision->translationValue)->toBeInstanceOf(TranslationValue::class);
        expect($revision->translationValue->id)->toBe($translationValue->id);
    });

    it('has timestamps', function () {
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

        $revision = TranslationRevision::create([
            'translation_value_id' => $translationValue->id,
            'old' => 'Old value',
            'new' => 'New value',
            'user_id' => $user->id
        ]);

        expect($revision->created_at)->not->toBeNull();
        expect($revision->updated_at)->not->toBeNull();
    });

    it('can be found by translation value id', function () {
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

        TranslationRevision::create([
            'translation_value_id' => $translationValue->id,
            'old' => 'Old value',
            'new' => 'New value',
            'user_id' => $user->id
        ]);

        $foundRevisions = TranslationRevision::where('translation_value_id', $translationValue->id)->get();

        expect($foundRevisions->count())->toBeGreaterThan(0);
        expect($foundRevisions->first()->translation_value_id)->toBe($translationValue->id);
    });

    it('can be found by user id', function () {
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

        TranslationRevision::create([
            'translation_value_id' => $translationValue->id,
            'old' => 'Old value',
            'new' => 'New value',
            'user_id' => $user->id
        ]);

        $foundRevisions = TranslationRevision::where('user_id', $user->id)->get();

        expect($foundRevisions->count())->toBeGreaterThan(0);
        expect($foundRevisions->first()->user_id)->toBe($user->id);
    });

    it('can be found by old value', function () {
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

        TranslationRevision::create([
            'translation_value_id' => $translationValue->id,
            'old' => 'Old value',
            'new' => 'New value',
            'user_id' => $user->id
        ]);

        $foundRevisions = TranslationRevision::where('old', 'Old value')->get();

        expect($foundRevisions->count())->toBeGreaterThan(0);
        expect($foundRevisions->first()->old)->toBe('Old value');
    });

    it('can be found by new value', function () {
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

        TranslationRevision::create([
            'translation_value_id' => $translationValue->id,
            'old' => 'Old value',
            'new' => 'New value',
            'user_id' => $user->id
        ]);

        $foundRevisions = TranslationRevision::where('new', 'New value')->get();

        expect($foundRevisions->count())->toBeGreaterThan(0);
        expect($foundRevisions->first()->new)->toBe('New value');
    });

    it('can be deleted', function () {
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

        $revision = TranslationRevision::create([
            'translation_value_id' => $translationValue->id,
            'old' => 'Old value',
            'new' => 'New value',
            'user_id' => $user->id
        ]);

        $revisionId = $revision->id;
        $revision->delete();

        expect(TranslationRevision::find($revisionId))->toBeNull();
    });

    it('can be mass assigned', function () {
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

        $revisionData = [
            'translation_value_id' => $translationValue->id,
            'old' => 'Old value',
            'new' => 'New value',
            'user_id' => $user->id
        ];

        $revision = TranslationRevision::create($revisionData);

        expect($revision->translation_value_id)->toBe($translationValue->id);
        expect($revision->old)->toBe('Old value');
        expect($revision->new)->toBe('New value');
        expect($revision->user_id)->toBe($user->id);
    });

    it('can be updated via mass assignment', function () {
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

        $revision = TranslationRevision::create([
            'translation_value_id' => $translationValue->id,
            'old' => 'Old value',
            'new' => 'New value',
            'user_id' => $user->id
        ]);

        $revision->fill(['new' => 'Updated value']);
        $revision->save();

        expect($revision->fresh()->new)->toBe('Updated value');
    });

    it('can handle long text values', function () {
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

        $longText = str_repeat('This is a very long text value that should be handled properly. ', 10);

        $revision = TranslationRevision::create([
            'translation_value_id' => $translationValue->id,
            'old' => $longText,
            'new' => 'New value',
            'user_id' => $user->id
        ]);

        expect($revision->old)->toBe($longText);
    });

    it('can be found by partial old value match', function () {
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

        TranslationRevision::create([
            'translation_value_id' => $translationValue->id,
            'old' => 'Old value',
            'new' => 'New value',
            'user_id' => $user->id
        ]);

        $foundRevisions = TranslationRevision::where('old', 'like', '%Old%')->get();

        expect($foundRevisions->count())->toBeGreaterThan(0);
        expect($foundRevisions->first()->old)->toContain('Old');
    });

    it('can be found by partial new value match', function () {
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

        TranslationRevision::create([
            'translation_value_id' => $translationValue->id,
            'old' => 'Old value',
            'new' => 'New value',
            'user_id' => $user->id
        ]);

        $foundRevisions = TranslationRevision::where('new', 'like', '%New%')->get();

        expect($foundRevisions->count())->toBeGreaterThan(0);
        expect($foundRevisions->first()->new)->toContain('New');
    });

    it('can handle multiple revisions for same translation value', function () {
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

        TranslationRevision::create([
            'translation_value_id' => $translationValue->id,
            'old' => 'Old value',
            'new' => 'New value',
            'user_id' => $user->id
        ]);

        TranslationRevision::create([
            'translation_value_id' => $translationValue->id,
            'old' => 'New value',
            'new' => 'Updated value',
            'user_id' => $user->id
        ]);

        $revisions = TranslationRevision::where('translation_value_id', $translationValue->id)->get();

        expect($revisions->count())->toBe(2);
    });

    it('can be ordered by creation date', function () {
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

        TranslationRevision::create([
            'translation_value_id' => $translationValue->id,
            'old' => 'Old value',
            'new' => 'New value',
            'user_id' => $user->id
        ]);

        $orderedRevisions = TranslationRevision::orderBy('created_at', 'desc')->get();

        expect($orderedRevisions->count())->toBeGreaterThan(0);
    });
}); 