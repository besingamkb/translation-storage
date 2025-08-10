<?php

use App\Repositories\TranslationRepository;
use App\Models\TranslationValue;
use App\Models\TranslationKey;
use App\Models\Locale;
use App\Models\TranslationTag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

describe('TranslationRepository', function () {
    beforeEach(function () {
        $this->repository = new TranslationRepository();
    });

    describe('all', function () {
        it('returns paginated translation values with relationships', function () {
            
            $locale = Locale::factory()->create();
            $translationKey = TranslationKey::factory()->create();
            $translationValue = TranslationValue::factory()->create([
                'translation_key_id' => $translationKey->id,
                'locale_id' => $locale->id,
            ]);

            $result = $this->repository->all(10);

            expect($result)->toBeInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class);
            expect($result->count())->toBe(1);
            expect($result->first()->id)->toBe($translationValue->id);
            expect($result->first()->translationKey)->not->toBeNull();
            expect($result->first()->locale)->not->toBeNull();
        });

        it('uses default pagination when not specified', function () {
            $result = $this->repository->all();

            expect($result->perPage())->toBe(20);
        });

        it('uses custom pagination when specified', function () {
            $result = $this->repository->all(15);

            expect($result->perPage())->toBe(15);
        });

        it('returns empty paginator when no data exists', function () {
            $result = $this->repository->all();

            expect($result->count())->toBe(0);
            expect($result->total())->toBe(0);
        });
    });

    describe('find', function () {
        it('finds translation value by id', function () {
            $translationValue = TranslationValue::factory()->create();

            $result = $this->repository->find($translationValue->id);

            expect($result)->toBeInstanceOf(TranslationValue::class);
            expect($result->id)->toBe($translationValue->id);
        });

        it('returns null for non-existent id', function () {
            $result = $this->repository->find(999);

            expect($result)->toBeNull();
        });
    });

    describe('create', function () {
        it('creates new translation value', function () {
            $locale = Locale::factory()->create();
            $translationKey = TranslationKey::factory()->create();
            
            $data = [
                'translation_key_id' => $translationKey->id,
                'locale_id' => $locale->id,
                'value' => 'Test translation value'
            ];

            $result = $this->repository->create($data);

            expect($result)->toBeInstanceOf(TranslationValue::class);
            expect($result->value)->toBe('Test translation value');
            expect($result->translation_key_id)->toBe($translationKey->id);
            expect($result->locale_id)->toBe($locale->id);
        });
    });

    describe('update', function () {
        it('updates existing translation value', function () {
            $translationValue = TranslationValue::factory()->create([
                'value' => 'Old value'
            ]);

            $data = ['value' => 'Updated value'];

            $result = $this->repository->update($translationValue->id, $data);

            expect($result)->toBeInstanceOf(TranslationValue::class);
            expect($result->value)->toBe('Updated value');
        });

        it('returns null for non-existent id', function () {
            $data = ['value' => 'Updated value'];

            $result = $this->repository->update(999, $data);

            expect($result)->toBeNull();
        });
    });

    describe('delete', function () {
        it('deletes existing translation value and returns true', function () {
            $translationValue = TranslationValue::factory()->create();

            $result = $this->repository->delete($translationValue->id);

            expect($result)->toBeTrue();
            expect(TranslationValue::find($translationValue->id))->toBeNull();
        });

        it('returns false for non-existent id', function () {
            $result = $this->repository->delete(999);

            expect($result)->toBeFalse();
        });
    });

    describe('searchWithFilters', function () {
        beforeEach(function () {
            
            $this->locale = Locale::factory()->create(['code' => 'en']);
            $this->translationKey = TranslationKey::factory()->create(['key' => 'welcome.message']);
            $this->translationValue = TranslationValue::factory()->create([
                'translation_key_id' => $this->translationKey->id,
                'locale_id' => $this->locale->id,
                'value' => 'Welcome to our application'
            ]);
        });

        it('returns query builder with basic joins', function () {
            $result = $this->repository->searchWithFilters();

            expect($result)->toBeInstanceOf(\Illuminate\Database\Query\Builder::class);
        });

        it('filters by tag', function () {
            $tag = TranslationTag::factory()->create(['name' => 'welcome']);
            $this->translationKey->tags()->attach($tag->id);

            $result = $this->repository->searchWithFilters(['tag' => 'welcome']);

            expect($result)->toBeInstanceOf(\Illuminate\Database\Query\Builder::class);
        });

        it('filters by key', function () {
            $result = $this->repository->searchWithFilters(['key' => 'welcome']);

            expect($result)->toBeInstanceOf(\Illuminate\Database\Query\Builder::class);
        });

        it('filters by content', function () {
            $result = $this->repository->searchWithFilters(['content' => 'Welcome']);

            expect($result)->toBeInstanceOf(\Illuminate\Database\Query\Builder::class);
        });

        it('filters by locale', function () {
            $result = $this->repository->searchWithFilters(['locale' => 'en']);

            expect($result)->toBeInstanceOf(\Illuminate\Database\Query\Builder::class);
        });

        it('combines multiple filters', function () {
            $result = $this->repository->searchWithFilters([
                'locale' => 'en',
                'key' => 'welcome',
                'content' => 'Welcome'
            ]);

            expect($result)->toBeInstanceOf(\Illuminate\Database\Query\Builder::class);
        });

        it('handles empty filters array', function () {
            $result = $this->repository->searchWithFilters([]);

            expect($result)->toBeInstanceOf(\Illuminate\Database\Query\Builder::class);
        });

        it('handles null filters', function () {
            $result = $this->repository->searchWithFilters(null);

            expect($result)->toBeInstanceOf(\Illuminate\Database\Query\Builder::class);
        });
    });

    describe('exportByLocale', function () {
        beforeEach(function () {
            $this->locale = Locale::factory()->create(['code' => 'en']);
            $this->translationKey1 = TranslationKey::factory()->create(['key' => 'welcome']);
            $this->translationKey2 = TranslationKey::factory()->create(['key' => 'goodbye']);
            
            $this->translationValue1 = TranslationValue::factory()->create([
                'translation_key_id' => $this->translationKey1->id,
                'locale_id' => $this->locale->id,
                'value' => 'Welcome message'
            ]);
            
            $this->translationValue2 = TranslationValue::factory()->create([
                'translation_key_id' => $this->translationKey2->id,
                'locale_id' => $this->locale->id,
                'value' => 'Goodbye message'
            ]);
        });

        it('exports translations for existing locale', function () {
            $result = $this->repository->exportByLocale('en');

            expect($result)->toBeArray();
            expect($result)->toHaveKey('welcome');
            expect($result)->toHaveKey('goodbye');
            expect($result['welcome'])->toBe('Welcome message');
            expect($result['goodbye'])->toBe('Goodbye message');
        });

        it('returns empty array for non-existent locale', function () {
            $result = $this->repository->exportByLocale('fr');

            expect($result)->toBeArray();
            expect($result)->toBeEmpty();
        });

        it('handles locale with no translations', function () {
            $emptyLocale = Locale::factory()->create(['code' => 'fr']);
            
            $result = $this->repository->exportByLocale('fr');

            expect($result)->toBeArray();
            expect($result)->toBeEmpty();
        });

        it('handles empty locale code', function () {
            $result = $this->repository->exportByLocale('');

            expect($result)->toBeArray();
            expect($result)->toBeEmpty();
        });

        it('handles null locale code', function () {
            $result = $this->repository->exportByLocale(null);

            expect($result)->toBeArray();
            expect($result)->toBeEmpty();
        });

        it('handles short locale code (2 characters)', function () {
            
            $longLocale = Locale::factory()->create(['code' => 'en_US']);
            $translationKey = TranslationKey::factory()->create(['key' => 'test']);
            TranslationValue::factory()->create([
                'translation_key_id' => $translationKey->id,
                'locale_id' => $longLocale->id,
                'value' => 'Test value'
            ]);

            $result = $this->repository->exportByLocale('en');

            expect($result)->toBeArray();
            
            
            
        });

        it('handles short locale code with no matching long codes', function () {
            $result = $this->repository->exportByLocale('xx');

            expect($result)->toBeArray();
            expect($result)->toBeEmpty();
        });
    });

    describe('getExportStats', function () {
        beforeEach(function () {
            $this->locale = Locale::factory()->create(['code' => 'en']);
            $this->translationKey1 = TranslationKey::factory()->create();
            $this->translationKey2 = TranslationKey::factory()->create();
            
            TranslationValue::factory()->create([
                'translation_key_id' => $this->translationKey1->id,
                'locale_id' => $this->locale->id,
                'value' => 'Short value'
            ]);
            
            TranslationValue::factory()->create([
                'translation_key_id' => $this->translationKey2->id,
                'locale_id' => $this->locale->id,
                'value' => 'Longer translation value'
            ]);
        });

        it('returns statistics for existing locale', function () {
            $result = $this->repository->getExportStats('en');

            expect($result)->toBeInstanceOf(\stdClass::class);
            expect($result->total_translations)->toBe(2);
            expect($result->unique_keys)->toBe(2);
            expect($result->avg_value_length)->toBeGreaterThan(0);
        });

        it('returns statistics for non-existent locale', function () {
            $result = $this->repository->getExportStats('fr');

            expect($result)->toBeInstanceOf(\stdClass::class);
            expect($result->total_translations)->toBe(0);
            expect($result->unique_keys)->toBe(0);
        });

        it('handles locale with single translation', function () {
            $singleLocale = Locale::factory()->create(['code' => 'fr']);
            $singleKey = TranslationKey::factory()->create();
            
            TranslationValue::factory()->create([
                'translation_key_id' => $singleKey->id,
                'locale_id' => $singleLocale->id,
                'value' => 'Single value'
            ]);

            $result = $this->repository->getExportStats('fr');

            expect($result->total_translations)->toBe(1);
            expect($result->unique_keys)->toBe(1);
        });

        it('handles empty locale code', function () {
            $result = $this->repository->getExportStats('');

            expect($result)->toBeInstanceOf(\stdClass::class);
            expect($result->total_translations)->toBe(0);
        });

        it('handles null locale code', function () {
            $result = $this->repository->getExportStats(null);

            expect($result)->toBeInstanceOf(\stdClass::class);
            expect($result->total_translations)->toBe(0);
        });

        it('handles short locale code (2 characters) for stats', function () {
            
            $longLocale = Locale::factory()->create(['code' => 'en_US']);
            $translationKey = TranslationKey::factory()->create();
            TranslationValue::factory()->create([
                'translation_key_id' => $translationKey->id,
                'locale_id' => $longLocale->id,
                'value' => 'Test value'
            ]);

            $result = $this->repository->getExportStats('en');

            expect($result)->toBeInstanceOf(\stdClass::class);
            
            
            
        });

        it('handles short locale code with no matching long codes for stats', function () {
            $result = $this->repository->getExportStats('xx');

            expect($result)->toBeInstanceOf(\stdClass::class);
            expect($result->total_translations)->toBe(0);
        });
    });
}); 