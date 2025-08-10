<?php

use App\Models\Locale;
use App\Models\TranslationKey;
use App\Models\TranslationValue;

beforeEach(function () {
    $this->locale = new Locale();
});

describe('Locale Model', function () {
    it('has correct fillable attributes', function () {
        expect($this->locale->getFillable())->toBe(['code', 'name']);
    });

    it('uses HasFactory trait', function () {
        expect(class_uses($this->locale))->toContain('Illuminate\Database\Eloquent\Factories\HasFactory');
    });

    it('can be created with fillable attributes', function () {
        $locale = Locale::create([
            'code' => 'en_US',
            'name' => 'English (United States)'
        ]);

        expect($locale->code)->toBe('en_US');
        expect($locale->name)->toBe('English (United States)');
        expect($locale->id)->toBeGreaterThan(0);
    });

    it('can be updated with fillable attributes', function () {
        $locale = Locale::create([
            'code' => 'en_US',
            'name' => 'English (United States)'
        ]);

        $locale->update(['name' => 'English (US)']);

        expect($locale->fresh()->name)->toBe('English (US)');
    });

    it('has translationValues relationship', function () {
        expect($this->locale->translationValues())->toBeInstanceOf('Illuminate\Database\Eloquent\Relations\HasMany');
    });

    it('has translationKeys relationship', function () {
        expect($this->locale->translationKeys())->toBeInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsToMany');
    });

    it('can access translationValues relationship', function () {
        $locale = Locale::create([
            'code' => 'en_US',
            'name' => 'English (United States)'
        ]);

        $translationKey = TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test key'
        ]);

        $translationValue = TranslationValue::create([
            'translation_key_id' => $translationKey->id,
            'locale_id' => $locale->id,
            'value' => 'Test value'
        ]);

        expect($locale->translationValues)->toBeInstanceOf('Illuminate\Database\Eloquent\Collection');
        expect($locale->translationValues->count())->toBe(1);
    });

    it('has timestamps', function () {
        $locale = Locale::create([
            'code' => 'en_US',
            'name' => 'English (United States)'
        ]);

        expect($locale->created_at)->not->toBeNull();
        expect($locale->updated_at)->not->toBeNull();
    });

    it('can be found by code', function () {
        Locale::create([
            'code' => 'en_US',
            'name' => 'English (United States)'
        ]);

        $foundLocale = Locale::where('code', 'en_US')->first();

        expect($foundLocale)->not->toBeNull();
        expect($foundLocale->code)->toBe('en_US');
    });

    it('can be found by name', function () {
        Locale::create([
            'code' => 'en_US',
            'name' => 'English (United States)'
        ]);

        $foundLocale = Locale::where('name', 'English (United States)')->first();

        expect($foundLocale)->not->toBeNull();
        expect($foundLocale->name)->toBe('English (United States)');
    });

    it('can be deleted', function () {
        $locale = Locale::create([
            'code' => 'en_US',
            'name' => 'English (United States)'
        ]);

        $localeId = $locale->id;
        $locale->delete();

        expect(Locale::find($localeId))->toBeNull();
    });

    it('can be mass assigned', function () {
        $localeData = [
            'code' => 'fr_FR',
            'name' => 'French (France)'
        ];

        $locale = Locale::create($localeData);

        expect($locale->code)->toBe('fr_FR');
        expect($locale->name)->toBe('French (France)');
    });

    it('can be updated via mass assignment', function () {
        $locale = Locale::create([
            'code' => 'en_US',
            'name' => 'English (United States)'
        ]);

        $locale->fill(['name' => 'English (US)']);
        $locale->save();

        expect($locale->fresh()->name)->toBe('English (US)');
    });
}); 