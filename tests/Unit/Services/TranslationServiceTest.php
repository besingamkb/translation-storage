<?php

use App\Services\TranslationService;
use App\Repositories\TranslationRepositoryInterface;
use App\Models\TranslationKey;
use App\Models\Locale;
use App\Models\TranslationTag;
use App\Models\TranslationValue;
use App\Models\TranslationRevision;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Mockery as M;

beforeEach(function () {
    $this->translationRepository = M::mock(TranslationRepositoryInterface::class);
    $this->translationService = new TranslationService($this->translationRepository);
});

describe('TranslationService', function () {
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

afterEach(function () {
    M::close();
});