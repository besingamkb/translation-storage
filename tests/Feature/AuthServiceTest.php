<?php

use App\Services\AuthService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Mockery as M;

uses(RefreshDatabase::class);

describe('AuthService Feature Tests', function () {
    beforeEach(function () {
        $this->authService = new AuthService();
    });

    describe('login', function () {
        it('successfully logs in with valid credentials', function () {
            
            $user = User::factory()->create([
                'email' => 'test@example.com',
                'password' => Hash::make('password123')
            ]);

            
            $request = new Request();
            $request->merge([
                'email' => 'test@example.com',
                'password' => 'password123'
            ]);

            $result = $this->authService->login($request);

            expect($result->getStatusCode())->toBe(200);
            expect($result->getData()->user->id)->toBe($user->id);
            expect($result->getData()->user->email)->toBe('test@example.com');
            expect($result->getData()->token)->toBeString();
        });

        it('returns 401 for non-existent user', function () {
            $request = new Request();
            $request->merge([
                'email' => 'nonexistent@example.com',
                'password' => 'password123'
            ]);

            $result = $this->authService->login($request);

            expect($result->getStatusCode())->toBe(401);
            expect($result->getData()->message)->toBe('Invalid credentials');
        });

        it('returns 401 for incorrect password', function () {
            
            $user = User::factory()->create([
                'email' => 'test@example.com',
                'password' => Hash::make('correctpassword')
            ]);

            $request = new Request();
            $request->merge([
                'email' => 'test@example.com',
                'password' => 'wrongpassword'
            ]);

            $result = $this->authService->login($request);

            expect($result->getStatusCode())->toBe(401);
            expect($result->getData()->message)->toBe('Invalid credentials');
        });

        it('validates request data correctly', function () {
            $request = new Request();
            $request->merge([
                'email' => 'test@example.com',
                'password' => 'password123'
            ]);

            $result = $this->authService->login($request);

            expect($result->getStatusCode())->toBe(401);
            expect($result->getData()->message)->toBe('Invalid credentials');
        });
    });

    describe('logout', function () {
        it('successfully logs out an authenticated user', function () {
            $user = M::mock(User::class);
            $token = M::mock();
            
            $user->shouldReceive('currentAccessToken')->once()->andReturn($token);
            $token->shouldReceive('delete')->once();

            $request = new Request();
            
            $request->setUserResolver(function () use ($user) {
                return $user;
            });

            $result = $this->authService->logout($request);

            expect($result->getData()->message)->toBe('Logged out successfully.');
        });

        it('returns 401 for unauthenticated user', function () {
            $request = new Request();
            
            $request->setUserResolver(function () {
                return null;
            });

            $result = $this->authService->logout($request);

            expect($result->getStatusCode())->toBe(401);
            expect($result->getData()->message)->toBe('Not authenticated.');
        });
    });
}); 