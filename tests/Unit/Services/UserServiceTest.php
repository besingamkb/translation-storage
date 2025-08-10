<?php

use App\Services\UserService;
use App\Repositories\UserRepositoryInterface;
use Mockery as M;

beforeEach(function () {
    $this->userRepository = M::mock(UserRepositoryInterface::class);
    $this->userService = new UserService($this->userRepository);
});

describe('UserService', function () {
    describe('list', function () {
        it('calls repository all with default pagination', function () {
            $this->userRepository->shouldReceive('all')->with(20)->once()->andReturn(['user1', 'user2']);
            $result = $this->userService->list();
            expect($result)->toBe(['user1', 'user2']);
        });

        it('calls repository all with custom pagination', function () {
            $this->userRepository->shouldReceive('all')->with(50)->once()->andReturn(['user1', 'user2', 'user3', 'user4']);
            $result = $this->userService->list(50);
            expect($result)->toBe(['user1', 'user2', 'user3', 'user4']);
        });

        it('handles empty results', function () {
            $this->userRepository->shouldReceive('all')->with(20)->once()->andReturn([]);
            $result = $this->userService->list();
            expect($result)->toBe([]);
        });

        it('handles single result', function () {
            $this->userRepository->shouldReceive('all')->with(20)->once()->andReturn(['user1']);
            $result = $this->userService->list();
            expect($result)->toBe(['user1']);
            expect(count($result))->toBe(1);
        });
    });

    describe('get', function () {
        it('calls repository find on get', function () {
            $this->userRepository->shouldReceive('find')->with(1)->once()->andReturn('user1');
            $result = $this->userService->get(1);
            expect($result)->toBe('user1');
        });

        it('handles non-existent id', function () {
            $this->userRepository->shouldReceive('find')->with(999)->once()->andReturn(null);
            $result = $this->userService->get(999);
            expect($result)->toBeNull();
        });

        it('handles string id', function () {
            $this->userRepository->shouldReceive('find')->with('user123')->once()->andReturn('user1');
            $result = $this->userService->get('user123');
            expect($result)->toBe('user1');
        });

        it('handles zero id', function () {
            $this->userRepository->shouldReceive('find')->with(0)->once()->andReturn(null);
            $result = $this->userService->get(0);
            expect($result)->toBeNull();
        });
    });

    describe('create', function () {
        it('calls repository create on create', function () {
            $data = ['name' => 'Test'];
            $this->userRepository->shouldReceive('create')->with($data)->once()->andReturn('created');
            $result = $this->userService->create($data);
            expect($result)->toBe('created');
        });

        it('handles complex user data', function () {
            $data = [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'hashedpassword',
                'role' => 'admin'
            ];
            $this->userRepository->shouldReceive('create')->with($data)->once()->andReturn('created');
            $result = $this->userService->create($data);
            expect($result)->toBe('created');
        });

        it('handles empty data array', function () {
            $data = [];
            $this->userRepository->shouldReceive('create')->with($data)->once()->andReturn('created');
            $result = $this->userService->create($data);
            expect($result)->toBe('created');
        });

        it('handles data with special characters', function () {
            $data = [
                'name' => 'José María',
                'email' => 'jose.maria@example.com'
            ];
            $this->userRepository->shouldReceive('create')->with($data)->once()->andReturn('created');
            $result = $this->userService->create($data);
            expect($result)->toBe('created');
        });
    });

    describe('update', function () {
        it('calls repository update on update', function () {
            $data = ['name' => 'Updated'];
            $this->userRepository->shouldReceive('update')->with(1, $data)->once()->andReturn('updated');
            $result = $this->userService->update(1, $data);
            expect($result)->toBe('updated');
        });

        it('handles partial updates', function () {
            $data = ['email' => 'newemail@example.com'];
            $this->userRepository->shouldReceive('update')->with(1, $data)->once()->andReturn('updated');
            $result = $this->userService->update(1, $data);
            expect($result)->toBe('updated');
        });

        it('handles string id updates', function () {
            $data = ['name' => 'Updated Name'];
            $this->userRepository->shouldReceive('update')->with('user123', $data)->once()->andReturn('updated');
            $result = $this->userService->update('user123', $data);
            expect($result)->toBe('updated');
        });

        it('handles update with empty data', function () {
            $data = [];
            $this->userRepository->shouldReceive('update')->with(1, $data)->once()->andReturn('updated');
            $result = $this->userService->update(1, $data);
            expect($result)->toBe('updated');
        });
    });

    describe('delete', function () {
        it('calls repository delete on delete', function () {
            $this->userRepository->shouldReceive('delete')->with(1)->once()->andReturn(true);
            $result = $this->userService->delete(1);
            expect($result)->toBeTrue();
        });

        it('handles delete failure', function () {
            $this->userRepository->shouldReceive('delete')->with(1)->once()->andReturn(false);
            $result = $this->userService->delete(1);
            expect($result)->toBeFalse();
        });

        it('handles string id deletion', function () {
            $this->userRepository->shouldReceive('delete')->with('user123')->once()->andReturn(true);
            $result = $this->userService->delete('user123');
            expect($result)->toBeTrue();
        });

        it('handles deletion of non-existent user', function () {
            $this->userRepository->shouldReceive('delete')->with(999)->once()->andReturn(false);
            $result = $this->userService->delete(999);
            expect($result)->toBeFalse();
        });
    });

    describe('edge cases', function () {
        it('handles zero pagination', function () {
            $this->userRepository->shouldReceive('all')->with(0)->once()->andReturn([]);
            $result = $this->userService->list(0);
            expect($result)->toBe([]);
        });

        it('handles negative pagination', function () {
            $this->userRepository->shouldReceive('all')->with(-5)->once()->andReturn([]);
            $result = $this->userService->list(-5);
            expect($result)->toBe([]);
        });

        it('handles very large pagination', function () {
            $this->userRepository->shouldReceive('all')->with(1000)->once()->andReturn(['user1', 'user2']);
            $result = $this->userService->list(1000);
            expect($result)->toBe(['user1', 'user2']);
        });
    });
});

afterEach(function () {
    M::close();
});
