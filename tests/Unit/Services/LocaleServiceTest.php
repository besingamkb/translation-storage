<?php

use App\Services\LocaleService;
use App\Repositories\LocaleRepositoryInterface;
use Mockery as M;

beforeEach(function () {
    $this->localeRepository = M::mock(LocaleRepositoryInterface::class);
    $this->localeService = new LocaleService($this->localeRepository);
});

describe('LocaleService', function () {
    describe('list', function () {
        it('calls repository all with default pagination', function () {
            $this->localeRepository->shouldReceive('all')->with(20)->once()->andReturn(['en', 'fr']);
            $result = $this->localeService->list();
            expect($result)->toBe(['en', 'fr']);
        });

        it('calls repository all with custom pagination', function () {
            $this->localeRepository->shouldReceive('all')->with(50)->once()->andReturn(['en', 'fr', 'es', 'de']);
            $result = $this->localeService->list(50);
            expect($result)->toBe(['en', 'fr', 'es', 'de']);
        });

        it('handles empty results', function () {
            $this->localeRepository->shouldReceive('all')->with(20)->once()->andReturn([]);
            $result = $this->localeService->list();
            expect($result)->toBe([]);
        });

        it('handles single result', function () {
            $this->localeRepository->shouldReceive('all')->with(20)->once()->andReturn(['en']);
            $result = $this->localeService->list();
            expect($result)->toBe(['en']);
            expect(count($result))->toBe(1);
        });
    });

    describe('get', function () {
        it('calls repository find on get', function () {
            $this->localeRepository->shouldReceive('find')->with(1)->once()->andReturn('en');
            $result = $this->localeService->get(1);
            expect($result)->toBe('en');
        });

        it('handles non-existent id', function () {
            $this->localeRepository->shouldReceive('find')->with(999)->once()->andReturn(null);
            $result = $this->localeService->get(999);
            expect($result)->toBeNull();
        });

        it('handles string id', function () {
            $this->localeRepository->shouldReceive('find')->with('en')->once()->andReturn('en');
            $result = $this->localeService->get('en');
            expect($result)->toBe('en');
        });
    });

    describe('create', function () {
        it('calls repository create on create', function () {
            $data = ['code' => 'es'];
            $this->localeRepository->shouldReceive('create')->with($data)->once()->andReturn('created');
            $result = $this->localeService->create($data);
            expect($result)->toBe('created');
        });

        it('handles complex locale data', function () {
            $data = [
                'code' => 'de',
                'name' => 'German',
                'description' => 'German language locale'
            ];
            $this->localeRepository->shouldReceive('create')->with($data)->once()->andReturn('created');
            $result = $this->localeService->create($data);
            expect($result)->toBe('created');
        });

        it('handles empty data array', function () {
            $data = [];
            $this->localeRepository->shouldReceive('create')->with($data)->once()->andReturn('created');
            $result = $this->localeService->create($data);
            expect($result)->toBe('created');
        });
    });

    describe('update', function () {
        it('calls repository update on update', function () {
            $data = ['code' => 'de'];
            $this->localeRepository->shouldReceive('update')->with(1, $data)->once()->andReturn('updated');
            $result = $this->localeService->update(1, $data);
            expect($result)->toBe('updated');
        });

        it('handles partial updates', function () {
            $data = ['name' => 'Updated German'];
            $this->localeRepository->shouldReceive('update')->with(1, $data)->once()->andReturn('updated');
            $result = $this->localeService->update(1, $data);
            expect($result)->toBe('updated');
        });

        it('handles string id updates', function () {
            $data = ['code' => 'fr'];
            $this->localeRepository->shouldReceive('update')->with('en', $data)->once()->andReturn('updated');
            $result = $this->localeService->update('en', $data);
            expect($result)->toBe('updated');
        });
    });

    describe('delete', function () {
        it('calls repository delete on delete', function () {
            $this->localeRepository->shouldReceive('delete')->with(1)->once()->andReturn(true);
            $result = $this->localeService->delete(1);
            expect($result)->toBeTrue();
        });

        it('handles delete failure', function () {
            $this->localeRepository->shouldReceive('delete')->with(1)->once()->andReturn(false);
            $result = $this->localeService->delete(1);
            expect($result)->toBeFalse();
        });

        it('handles string id deletion', function () {
            $this->localeRepository->shouldReceive('delete')->with('en')->once()->andReturn(true);
            $result = $this->localeService->delete('en');
            expect($result)->toBeTrue();
        });
    });

    describe('edge cases', function () {
        it('handles zero pagination', function () {
            $this->localeRepository->shouldReceive('all')->with(0)->once()->andReturn([]);
            $result = $this->localeService->list(0);
            expect($result)->toBe([]);
        });

        it('handles negative pagination', function () {
            $this->localeRepository->shouldReceive('all')->with(-5)->once()->andReturn([]);
            $result = $this->localeService->list(-5);
            expect($result)->toBe([]);
        });

        it('handles very large pagination', function () {
            $this->localeRepository->shouldReceive('all')->with(1000)->once()->andReturn(['en', 'fr']);
            $result = $this->localeService->list(1000);
            expect($result)->toBe(['en', 'fr']);
        });
    });
});

afterEach(function () {
    M::close();
});