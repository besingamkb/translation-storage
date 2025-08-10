<?php

use App\Repositories\LocaleRepository;
use App\Models\Locale;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('LocaleRepository', function () {
    beforeEach(function () {
        $this->repository = new LocaleRepository();
    });

    describe('all', function () {
        it('returns paginated locales', function () {
            
            $locale1 = Locale::factory()->create(['code' => 'en', 'name' => 'English']);
            $locale2 = Locale::factory()->create(['code' => 'fr', 'name' => 'French']);

            $result = $this->repository->all(10);

            expect($result)->toBeInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class);
            expect($result->count())->toBe(2);
            expect($result->first()->code)->toBe('en');
            expect($result->first()->name)->toBe('English');
            expect($result->last()->code)->toBe('fr');
            expect($result->last()->name)->toBe('French');
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

        it('handles large pagination values', function () {
            $result = $this->repository->all(100);

            expect($result->perPage())->toBe(100);
        });
    });

    describe('find', function () {
        it('finds locale by id', function () {
            $locale = Locale::factory()->create(['code' => 'en', 'name' => 'English']);

            $result = $this->repository->find($locale->id);

            expect($result)->toBeInstanceOf(Locale::class);
            expect($result->id)->toBe($locale->id);
            expect($result->code)->toBe('en');
            expect($result->name)->toBe('English');
        });

        it('returns null for non-existent id', function () {
            $result = $this->repository->find(999);

            expect($result)->toBeNull();
        });

        it('returns null for zero id', function () {
            $result = $this->repository->find(0);

            expect($result)->toBeNull();
        });

        it('returns null for negative id', function () {
            $result = $this->repository->find(-1);

            expect($result)->toBeNull();
        });

        it('returns null for string id', function () {
            $result = $this->repository->find('invalid');

            expect($result)->toBeNull();
        });
    });

    describe('create', function () {
        it('creates new locale', function () {
            $data = [
                'code' => 'es',
                'name' => 'Spanish'
            ];

            $result = $this->repository->create($data);

            expect($result)->toBeInstanceOf(Locale::class);
            expect($result->code)->toBe('es');
            expect($result->name)->toBe('Spanish');
            expect($result->id)->toBeGreaterThan(0);
        });

        it('creates locale with minimal data', function () {
            $data = [
                'code' => 'de',
                'name' => 'German'
            ];

            $result = $this->repository->create($data);

            expect($result)->toBeInstanceOf(Locale::class);
            expect($result->code)->toBe('de');
            expect($result->name)->toBe('German');
        });

        it('creates locale with special characters', function () {
            $data = [
                'code' => 'zh',
                'name' => '中文 (Chinese)'
            ];

            $result = $this->repository->create($data);

            expect($result)->toBeInstanceOf(Locale::class);
            expect($result->code)->toBe('zh');
            expect($result->name)->toBe('中文 (Chinese)');
        });

        it('creates locale with long name', function () {
            $longName = str_repeat('A', 255); 
            $data = [
                'code' => 'xx',
                'name' => $longName
            ];

            $result = $this->repository->create($data);

            expect($result)->toBeInstanceOf(Locale::class);
            expect($result->code)->toBe('xx');
            expect($result->name)->toBe($longName);
        });
    });

    describe('update', function () {
        it('updates existing locale', function () {
            $locale = Locale::factory()->create([
                'code' => 'en',
                'name' => 'English'
            ]);

            $data = [
                'name' => 'Updated English'
            ];

            $result = $this->repository->update($locale->id, $data);

            expect($result)->toBeInstanceOf(Locale::class);
            expect($result->code)->toBe('en');
            expect($result->name)->toBe('Updated English');
        });

        it('updates locale with multiple fields', function () {
            $locale = Locale::factory()->create([
                'code' => 'fr',
                'name' => 'French'
            ]);

            $data = [
                'code' => 'fr_FR',
                'name' => 'French (France)'
            ];

            $result = $this->repository->update($locale->id, $data);

            expect($result)->toBeInstanceOf(Locale::class);
            expect($result->code)->toBe('fr_FR');
            expect($result->name)->toBe('French (France)');
        });

        it('returns null for non-existent id', function () {
            $data = ['name' => 'Updated Name'];

            $result = $this->repository->update(999, $data);

            expect($result)->toBeNull();
        });

        it('returns null for zero id', function () {
            $data = ['name' => 'Updated Name'];

            $result = $this->repository->update(0, $data);

            expect($result)->toBeNull();
        });

        it('returns null for negative id', function () {
            $data = ['name' => 'Updated Name'];

            $result = $this->repository->update(-1, $data);

            expect($result)->toBeNull();
        });

        it('handles empty update data', function () {
            $locale = Locale::factory()->create([
                'code' => 'en',
                'name' => 'English'
            ]);

            $result = $this->repository->update($locale->id, []);

            expect($result)->toBeInstanceOf(Locale::class);
            expect($result->code)->toBe('en');
            expect($result->name)->toBe('English');
        });

        it('handles partial updates', function () {
            $locale = Locale::factory()->create([
                'code' => 'en',
                'name' => 'English'
            ]);

            $result = $this->repository->update($locale->id, ['code' => 'en_US']);

            expect($result)->toBeInstanceOf(Locale::class);
            expect($result->code)->toBe('en_US');
            expect($result->name)->toBe('English'); 
        });
    });

    describe('delete', function () {
        it('deletes existing locale and returns true', function () {
            $locale = Locale::factory()->create(['code' => 'en', 'name' => 'English']);

            $result = $this->repository->delete($locale->id);

            expect($result)->toBeTrue();
            expect(Locale::find($locale->id))->toBeNull();
        });

        it('returns false for non-existent id', function () {
            $result = $this->repository->delete(999);

            expect($result)->toBeFalse();
        });

        it('returns false for zero id', function () {
            $result = $this->repository->delete(0);

            expect($result)->toBeFalse();
        });

        it('returns false for negative id', function () {
            $result = $this->repository->delete(-1);

            expect($result)->toBeFalse();
        });

        it('returns false for string id', function () {
            $result = $this->repository->delete('invalid');

            expect($result)->toBeFalse();
        });

        it('deletes locale and verifies it is removed from database', function () {
            $locale = Locale::factory()->create(['code' => 'fr', 'name' => 'French']);

            $result = $this->repository->delete($locale->id);

            expect($result)->toBeTrue();
            
            
            $deletedLocale = Locale::find($locale->id);
            expect($deletedLocale)->toBeNull();
            
            
            $otherLocale = Locale::factory()->create(['code' => 'es', 'name' => 'Spanish']);
            $foundLocale = Locale::find($otherLocale->id);
            expect($foundLocale)->not->toBeNull();
        });
    });

    describe('edge cases', function () {
        it('handles locales with empty name', function () {
            $data = [
                'code' => 'xx',
                'name' => ''
            ];

            $result = $this->repository->create($data);

            expect($result)->toBeInstanceOf(Locale::class);
            expect($result->code)->toBe('xx');
            expect($result->name)->toBe('');
        });

        it('handles very short locale codes', function () {
            $data = [
                'code' => 'a',
                'name' => 'Short Code'
            ];

            $result = $this->repository->create($data);

            expect($result)->toBeInstanceOf(Locale::class);
            expect($result->code)->toBe('a');
            expect($result->name)->toBe('Short Code');
        });

        it('handles very long locale codes', function () {
            $longCode = str_repeat('x', 10); 
            $data = [
                'code' => $longCode,
                'name' => 'Long Code'
            ];

            $result = $this->repository->create($data);

            expect($result)->toBeInstanceOf(Locale::class);
            expect($result->code)->toBe($longCode);
            expect($result->name)->toBe('Long Code');
        });

        it('handles locale codes at maximum length', function () {
            $maxCode = str_repeat('x', 10); 
            $data = [
                'code' => $maxCode,
                'name' => 'Max Length Code'
            ];

            $result = $this->repository->create($data);

            expect($result)->toBeInstanceOf(Locale::class);
            expect($result->code)->toBe($maxCode);
            expect($result->name)->toBe('Max Length Code');
        });
    });

    describe('data integrity', function () {
        it('maintains data integrity after create and update', function () {
            
            $createData = [
                'code' => 'pt',
                'name' => 'Portuguese'
            ];
            $created = $this->repository->create($createData);

            expect($created->code)->toBe('pt');
            expect($created->name)->toBe('Portuguese');

            
            $updateData = [
                'name' => 'Portuguese (Brazil)'
            ];
            $updated = $this->repository->update($created->id, $updateData);

            expect($updated->code)->toBe('pt');
            expect($updated->name)->toBe('Portuguese (Brazil)');

            
            $found = $this->repository->find($created->id);
            expect($found->code)->toBe('pt');
            expect($found->name)->toBe('Portuguese (Brazil)');
        });

        it('handles concurrent operations', function () {
            
            $locale1 = $this->repository->create(['code' => 'en', 'name' => 'English']);
            $locale2 = $this->repository->create(['code' => 'fr', 'name' => 'French']);
            $locale3 = $this->repository->create(['code' => 'es', 'name' => 'Spanish']);

            
            $this->repository->update($locale2->id, ['name' => 'French (Updated)']);

            
            $this->repository->delete($locale3->id);

            
            $all = $this->repository->all();
            expect($all->count())->toBe(2);

            $found1 = $this->repository->find($locale1->id);
            expect($found1->name)->toBe('English');

            $found2 = $this->repository->find($locale2->id);
            expect($found2->name)->toBe('French (Updated)');

            $found3 = $this->repository->find($locale3->id);
            expect($found3)->toBeNull();
        });
    });
}); 