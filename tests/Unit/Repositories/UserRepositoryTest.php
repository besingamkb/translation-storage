<?php

use App\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('UserRepository', function () {
    beforeEach(function () {
        $this->repository = new UserRepository();
    });

    describe('all', function () {
        it('returns paginated users', function () {
            $user1 = User::factory()->create(['name' => 'Alice', 'email' => 'alice@example.com']);
            $user2 = User::factory()->create(['name' => 'Bob', 'email' => 'bob@example.com']);

            $result = $this->repository->all(10);

            expect($result)->toBeInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class);
            expect($result->count())->toBe(2);
            expect($result->first()->name)->toBe('Alice');
            expect($result->last()->name)->toBe('Bob');
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
        it('finds user by id', function () {
            $user = User::factory()->create(['name' => 'Alice', 'email' => 'alice@example.com']);
            $result = $this->repository->find($user->id);
            expect($result)->toBeInstanceOf(User::class);
            expect($result->id)->toBe($user->id);
            expect($result->name)->toBe('Alice');
            expect($result->email)->toBe('alice@example.com');
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
        it('creates new user', function () {
            $data = [
                'name' => 'Charlie',
                'email' => 'charlie@example.com',
                'password' => bcrypt('secret'),
            ];
            $result = $this->repository->create($data);
            expect($result)->toBeInstanceOf(User::class);
            expect($result->name)->toBe('Charlie');
            expect($result->email)->toBe('charlie@example.com');
            expect($result->id)->toBeGreaterThan(0);
        });

        it('creates user with minimal data', function () {
            $data = [
                'name' => 'Dana',
                'email' => 'dana@example.com',
                'password' => bcrypt('secret'),
            ];
            $result = $this->repository->create($data);
            expect($result)->toBeInstanceOf(User::class);
            expect($result->name)->toBe('Dana');
            expect($result->email)->toBe('dana@example.com');
        });

        it('creates user with special characters', function () {
            $data = [
                'name' => 'Élodie',
                'email' => 'elodie@example.com',
                'password' => bcrypt('secret'),
            ];
            $result = $this->repository->create($data);
            expect($result)->toBeInstanceOf(User::class);
            expect($result->name)->toBe('Élodie');
            expect($result->email)->toBe('elodie@example.com');
        });
    });

    describe('update', function () {
        it('updates existing user', function () {
            $user = User::factory()->create([
                'name' => 'Eve',
                'email' => 'eve@example.com',
                'password' => bcrypt('secret'),
            ]);
            $data = [
                'name' => 'Eve Updated',
            ];
            $result = $this->repository->update($user->id, $data);
            expect($result)->toBeInstanceOf(User::class);
            expect($result->name)->toBe('Eve Updated');
            expect($result->email)->toBe('eve@example.com');
        });

        it('returns null for non-existent id', function () {
            $data = ['name' => 'Updated Name'];
            $result = $this->repository->update(999, $data);
            expect($result)->toBeNull();
        });

        it('handles empty update data', function () {
            $user = User::factory()->create([
                'name' => 'Frank',
                'email' => 'frank@example.com',
                'password' => bcrypt('secret'),
            ]);
            $result = $this->repository->update($user->id, []);
            expect($result)->toBeInstanceOf(User::class);
            expect($result->name)->toBe('Frank');
            expect($result->email)->toBe('frank@example.com');
        });
    });

    describe('delete', function () {
        it('deletes existing user and returns true', function () {
            $user = User::factory()->create(['name' => 'Grace', 'email' => 'grace@example.com']);
            $result = $this->repository->delete($user->id);
            expect($result)->toBeTrue();
            expect(User::find($user->id))->toBeNull();
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
    });
});
