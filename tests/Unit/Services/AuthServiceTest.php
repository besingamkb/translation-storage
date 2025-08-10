<?php

use App\Services\AuthService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Mockery as M;

beforeEach(function () {
    $this->request = M::mock(Request::class);
    $this->authService = new AuthService();
});

describe('AuthService', function () {
    describe('logout', function () {
        it('successfully logs out an authenticated user', function () {
            $user = M::mock(User::class);
            $token = M::mock(PersonalAccessToken::class);
            
            $this->request->shouldReceive('user')->once()->andReturn($user);
            $user->shouldReceive('currentAccessToken')->once()->andReturn($token);
            $token->shouldReceive('delete')->once();
            
            $result = $this->authService->logout($this->request);
            
            expect($result->getData()->message)->toBe('Logged out successfully.');
        });

        it('returns 401 for unauthenticated user', function () {
            $this->request->shouldReceive('user')->once()->andReturn(null);
            
            $result = $this->authService->logout($this->request);
            
            expect($result->getStatusCode())->toBe(401);
            expect($result->getData()->message)->toBe('Not authenticated.');
        });
    });
});

afterEach(function () {
    M::close();
}); 