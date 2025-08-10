<?php

use App\Services\TranslationService;
use App\Repositories\TranslationRepositoryInterface;
use App\Models\TranslationKey;
use App\Models\Locale;
use App\Models\TranslationTag;
use App\Models\TranslationValue;
use App\Models\TranslationRevision;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mockery as M;

uses(RefreshDatabase::class);

describe('TranslationService Feature Tests', function () {
    beforeEach(function () {
        $this->translationRepository = M::mock(TranslationRepositoryInterface::class);
        $this->translationService = new TranslationService($this->translationRepository);
    });

    describe('store', function () {
        it('creates new translation key and value', function () {
            
            $locale = Locale::factory()->create(['code' => 'en']);

            $validated = [
                'key' => 'welcome.message',
                'value' => 'Welcome to our app!',
                'locale' => 'en',
                'description' => 'Welcome message',
                'tags' => ['welcome', 'message']
            ];

            $result = $this->translationService->store($validated);

            expect($result)->toBeInstanceOf(TranslationValue::class);
            expect($result->value)->toBe('Welcome to our app!');
            expect($result->translationKey->key)->toBe('welcome.message');
            expect($result->translationKey->description)->toBe('Welcome message');
            expect($result->locale->code)->toBe('en');
            
            
            expect($result->translationKey->tags)->toHaveCount(2);
            expect($result->translationKey->tags->pluck('name')->toArray())->toContain('welcome');
            expect($result->translationKey->tags->pluck('name')->toArray())->toContain('message');
        });

        it('updates existing translation value', function () {
            
            $locale = Locale::factory()->create(['code' => 'en']);
            
            
            $translationKey = TranslationKey::factory()->create([
                'key' => 'welcome.message',
                'description' => 'Welcome message'
            ]);
            
            $translationValue = TranslationValue::factory()->create([
                'translation_key_id' => $translationKey->id,
                'locale_id' => $locale->id,
                'value' => 'Old welcome message'
            ]);

            $validated = [
                'key' => 'welcome.message',
                'value' => 'Updated welcome message',
                'locale' => 'en'
            ];

            $result = $this->translationService->store($validated);

            expect($result)->toBeInstanceOf(TranslationValue::class);
            expect($result->value)->toBe('Updated welcome message');
            expect($result->translationKey->key)->toBe('welcome.message');
            expect($result->locale->code)->toBe('en');
        });

        it('creates translation without tags', function () {
            
            $locale = Locale::factory()->create(['code' => 'en']);

            $validated = [
                'key' => 'simple.key',
                'value' => 'Simple value',
                'locale' => 'en'
            ];

            $result = $this->translationService->store($validated);

            expect($result)->toBeInstanceOf(TranslationValue::class);
            expect($result->value)->toBe('Simple value');
            expect($result->translationKey->key)->toBe('simple.key');
            expect($result->translationKey->tags)->toHaveCount(0);
        });

        it('handles existing tags when creating translation', function () {
            
            $locale = Locale::factory()->create(['code' => 'en']);
            
            
            $existingTag = TranslationTag::factory()->create(['name' => 'welcome']);

            $validated = [
                'key' => 'welcome.message',
                'value' => 'Welcome message',
                'locale' => 'en',
                'tags' => ['welcome', 'new-tag']
            ];

            $result = $this->translationService->store($validated);

            expect($result)->toBeInstanceOf(TranslationValue::class);
            expect($result->translationKey->tags)->toHaveCount(2);
            expect($result->translationKey->tags->pluck('name')->toArray())->toContain('welcome');
            expect($result->translationKey->tags->pluck('name')->toArray())->toContain('new-tag');
        });
    });

    describe('update', function () {
        it('updates translation value and creates revision when user provided', function () {
            $user = User::factory()->create();
            $translationValue = TranslationValue::factory()->create([
                'value' => 'Old value'
            ]);

            
            $this->translationRepository->shouldReceive('find')
                ->with($translationValue->id)
                ->once()
                ->andReturn($translationValue);

            $validated = ['value' => 'Updated value'];

            $result = $this->translationService->update($validated, $translationValue->id, $user);

            expect($result)->toBeInstanceOf(TranslationValue::class);
            expect($result->value)->toBe('Updated value');
            
            
            $revision = TranslationRevision::where('translation_value_id', $translationValue->id)->first();
            expect($revision)->not->toBeNull();
            expect($revision->old)->toBe('Old value');
            expect($revision->new)->toBe('Updated value');
            expect($revision->user_id)->toBe($user->id);
        });

        it('updates translation value without revision when no user provided', function () {
            $translationValue = TranslationValue::factory()->create([
                'value' => 'Old value'
            ]);

            
            $this->translationRepository->shouldReceive('find')
                ->with($translationValue->id)
                ->once()
                ->andReturn($translationValue);

            $validated = ['value' => 'Updated value'];

            $result = $this->translationService->update($validated, $translationValue->id);

            expect($result)->toBeInstanceOf(TranslationValue::class);
            expect($result->value)->toBe('Updated value');
            
            
            $revision = TranslationRevision::where('translation_value_id', $translationValue->id)->first();
            expect($revision)->toBeNull();
        });

        it('loads relationships after update', function () {
            $translationValue = TranslationValue::factory()->create([
                'value' => 'Old value'
            ]);

            
            $this->translationRepository->shouldReceive('find')
                ->with($translationValue->id)
                ->once()
                ->andReturn($translationValue);

            $validated = ['value' => 'Updated value'];

            $result = $this->translationService->update($validated, $translationValue->id);

            expect($result->translationKey)->not->toBeNull();
            expect($result->locale)->not->toBeNull();
        });
    });

    describe('destroy', function () {
        it('deletes translation value', function () {
            $id = 1;
            $this->translationRepository->shouldReceive('delete')->with(1)->once()->andReturn(true);

            $result = $this->translationService->destroy($id);

            expect($result)->toBeTrue();
        });
    });

    describe('search', function () {
        it('searches translations with filters and pagination', function () {
            $filters = ['locale' => 'en', 'key' => 'welcome'];
            $perPage = 15;

            $query = M::mock();
            $query->shouldReceive('paginate')->with(15)->once()->andReturn(['results']);

            $this->translationRepository->shouldReceive('searchWithFilters')
                ->with($filters)
                ->once()
                ->andReturn($query);

            $result = $this->translationService->search($filters, $perPage);

            expect($result)->toBe(['results']);
        });
    });

    describe('exportTranslations', function () {
        it('returns error when no locales exist', function () {
            $baseUrl = 'https://example.com';

            $dbTable = M::mock();
            $dbTable->shouldReceive('pluck')->with('code')->andReturnSelf();
            $dbTable->shouldReceive('toArray')->andReturn([]);
            
            DB::shouldReceive('table')->with('locales')->andReturn($dbTable);

            $result = $this->translationService->exportTranslations(null, $baseUrl);

            expect($result['error'])->toBe('No locales found.');
            expect($result['status'])->toBe(404);
        });

        it('exports translations for specific locale', function () {
            $locale = 'en';
            $baseUrl = 'https://example.com';

            $dbTable = M::mock();
            $dbTable->shouldReceive('pluck')->with('code')->andReturnSelf();
            $dbTable->shouldReceive('toArray')->andReturn(['en', 'fr', 'es']);

            $translations = ['key1' => 'value1', 'key2' => 'value2'];

            DB::shouldReceive('table')->with('locales')->andReturn($dbTable);
            $this->translationRepository->shouldReceive('exportByLocale')->with('en')->andReturn($translations);

            $result = $this->translationService->exportTranslations($locale, $baseUrl);

            expect($result['data'])->toBe($translations);
            expect($result['meta']['current_locale'])->toBe('en');
            expect($result['meta']['other_locales'])->toHaveKeys(['fr', 'es']);
            expect($result['status'])->toBe(200);
        });

        it('exports translations for first locale when none specified', function () {
            $baseUrl = 'https://example.com';

            $dbTable = M::mock();
            $dbTable->shouldReceive('pluck')->with('code')->andReturnSelf();
            $dbTable->shouldReceive('toArray')->andReturn(['en', 'fr']);

            $translations = ['key1' => 'value1'];

            DB::shouldReceive('table')->with('locales')->andReturn($dbTable);
            $this->translationRepository->shouldReceive('exportByLocale')->with('en')->andReturn($translations);

            $result = $this->translationService->exportTranslations(null, $baseUrl);

            expect($result['data'])->toBe($translations);
            expect($result['meta']['current_locale'])->toBe('en');
            expect($result['status'])->toBe(200);
        });
    });

    describe('getExportStats', function () {
        it('gets export statistics for locale', function () {
            $locale = 'en';
            $stats = ['total_keys' => 100, 'translated_keys' => 80];

            $this->translationRepository->shouldReceive('getExportStats')->with('en')->once()->andReturn($stats);

            $result = $this->translationService->getExportStats($locale);

            expect($result)->toBe($stats);
        });
    });
}); 