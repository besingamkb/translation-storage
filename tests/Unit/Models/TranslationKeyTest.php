<?php

use App\Models\Locale;
use App\Models\TranslationKey;
use App\Models\TranslationValue;

beforeEach(function () {
    $this->translationKey = new TranslationKey();
});

describe('TranslationKey Model', function () {
    it('has correct fillable attributes', function () {
        expect($this->translationKey->getFillable())->toBe(['key', 'description']);
    });

    it('uses HasFactory trait', function () {
        expect(class_uses($this->translationKey))->toContain('Illuminate\Database\Eloquent\Factories\HasFactory');
    });

    it('can be created with fillable attributes', function () {
        $translationKey = TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test description'
        ]);

        expect($translationKey->key)->toBe('test.key');
        expect($translationKey->description)->toBe('Test description');
        expect($translationKey->id)->toBeGreaterThan(0);
    });

    it('can be updated with fillable attributes', function () {
        $translationKey = TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test description'
        ]);

        $translationKey->update(['description' => 'Updated description']);

        expect($translationKey->fresh()->description)->toBe('Updated description');
    });

    it('has translationValues relationship', function () {
        expect($this->translationKey->translationValues())->toBeInstanceOf('Illuminate\Database\Eloquent\Relations\HasMany');
    });

    it('has tags relationship', function () {
        expect($this->translationKey->tags())->toBeInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsToMany');
    });

    it('can access locale through translation values', function () {
        $locale = Locale::create([
            'code' => 'en_US',
            'name' => 'English (United States)'
        ]);

        $translationKey = TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test description'
        ]);

        $translationValue = TranslationValue::create([
            'translation_key_id' => $translationKey->id,
            'locale_id' => $locale->id,
            'value' => 'Test value'
        ]);

        
        expect($translationKey->translationValues->first()->locale)->toBeInstanceOf('App\Models\Locale');
        expect($translationKey->translationValues->first()->locale->code)->toBe('en_US');
    });

    it('can access translationValues relationship', function () {
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

        expect($translationKey->translationValues)->toBeInstanceOf('Illuminate\Database\Eloquent\Collection');
        expect($translationKey->translationValues->count())->toBe(1);
    });

    it('can access tags relationship', function () {
        $translationKey = TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test description'
        ]);

        expect($translationKey->tags)->toBeInstanceOf('Illuminate\Database\Eloquent\Collection');
    });

    it('can sync tags without detaching', function () {
        $translationKey = TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test description'
        ]);

        
        $tag1 = \App\Models\TranslationTag::create(['name' => 'UI', 'description' => 'User interface']);
        $tag2 = \App\Models\TranslationTag::create(['name' => 'Email', 'description' => 'Email related']);
        $tag3 = \App\Models\TranslationTag::create(['name' => 'System', 'description' => 'System messages']);

        $translationKey->tags()->sync([$tag1->id, $tag2->id, $tag3->id]);

        expect($translationKey->tags->pluck('id')->toArray())->toContain($tag1->id);
        expect($translationKey->tags->pluck('id')->toArray())->toContain($tag2->id);
        expect($translationKey->tags->pluck('id')->toArray())->toContain($tag3->id);
    });

    it('can sync tags with detaching', function () {
        $translationKey = TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test description'
        ]);

        
        $tag1 = \App\Models\TranslationTag::create(['name' => 'UI', 'description' => 'User interface']);
        $tag2 = \App\Models\TranslationTag::create(['name' => 'Email', 'description' => 'Email related']);
        $tag3 = \App\Models\TranslationTag::create(['name' => 'System', 'description' => 'System messages']);
        $tag4 = \App\Models\TranslationTag::create(['name' => 'Error', 'description' => 'Error messages']);

        $translationKey->tags()->sync([$tag1->id, $tag2->id, $tag3->id]);

        $translationKey->tags()->sync([$tag2->id, $tag3->id, $tag4->id], false);

        expect($translationKey->tags->pluck('id')->toArray())->toContain($tag1->id);
        expect($translationKey->tags->pluck('id')->toArray())->toContain($tag2->id);
        expect($translationKey->tags->pluck('id')->toArray())->toContain($tag3->id);
        expect($translationKey->tags->pluck('id')->toArray())->toContain($tag4->id);
    });

    it('has timestamps', function () {
        $translationKey = TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test description'
        ]);

        expect($translationKey->created_at)->not->toBeNull();
        expect($translationKey->updated_at)->not->toBeNull();
    });

    it('can be found by key', function () {
        TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test description'
        ]);

        $foundKey = TranslationKey::where('key', 'test.key')->first();

        expect($foundKey)->not->toBeNull();
        expect($foundKey->key)->toBe('test.key');
    });

    it('can be found by description', function () {
        TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test description'
        ]);

        $foundKey = TranslationKey::where('description', 'Test description')->first();

        expect($foundKey)->not->toBeNull();
        expect($foundKey->description)->toBe('Test description');
    });

    it('can be deleted', function () {
        $translationKey = TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test description'
        ]);

        $keyId = $translationKey->id;
        $translationKey->delete();

        expect(TranslationKey::find($keyId))->toBeNull();
    });

    it('can be mass assigned', function () {
        $keyData = [
            'key' => 'new.key',
            'description' => 'New description'
        ];

        $translationKey = TranslationKey::create($keyData);

        expect($translationKey->key)->toBe('new.key');
        expect($translationKey->description)->toBe('New description');
    });

    it('can be updated via mass assignment', function () {
        $translationKey = TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test description'
        ]);

        $translationKey->fill(['description' => 'Updated description']);
        $translationKey->save();

        expect($translationKey->fresh()->description)->toBe('Updated description');
    });

    it('can handle null description', function () {
        $translationKey = TranslationKey::create([
            'key' => 'test.key',
            'description' => null
        ]);

        expect($translationKey->description)->toBeNull();
    });

    it('can be found by partial key match', function () {
        TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test description'
        ]);

        $foundKeys = TranslationKey::where('key', 'like', '%test%')->get();

        expect($foundKeys->count())->toBeGreaterThan(0);
        expect($foundKeys->first()->key)->toContain('test');
    });
}); 