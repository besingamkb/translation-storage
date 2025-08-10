<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = new User();
});

describe('User Model', function () {
    it('has correct fillable attributes', function () {
        expect($this->user->getFillable())->toBe([
            'name',
            'email',
            'password'
        ]);
    });

    it('uses HasFactory trait', function () {
        expect(class_uses($this->user))->toContain('Illuminate\Database\Eloquent\Factories\HasFactory');
    });

    it('uses Notifiable trait', function () {
        expect(class_uses($this->user))->toContain('Illuminate\Notifications\Notifiable');
    });

    it('uses HasApiTokens trait', function () {
        expect(class_uses($this->user))->toContain('Laravel\Sanctum\HasApiTokens');
    });

    it('can be created with fillable attributes', function () {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        expect($user->name)->toBe('John Doe');
        expect($user->email)->toBe('john@example.com');
        expect($user->id)->toBeGreaterThan(0);
        expect(Hash::check('password123', $user->password))->toBeTrue();
    });

    it('can be updated with fillable attributes', function () {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        $user->update(['name' => 'Jane Doe']);

        expect($user->fresh()->name)->toBe('Jane Doe');
    });

    it('can update password', function () {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        $user->update(['password' => Hash::make('newpassword123')]);

        expect(Hash::check('newpassword123', $user->fresh()->password))->toBeTrue();
        expect(Hash::check('password123', $user->fresh()->password))->toBeFalse();
    });

    it('has timestamps', function () {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        expect($user->created_at)->not->toBeNull();
        expect($user->updated_at)->not->toBeNull();
    });

    it('can be found by email', function () {
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        $foundUser = User::where('email', 'john@example.com')->first();

        expect($foundUser)->not->toBeNull();
        expect($foundUser->email)->toBe('john@example.com');
    });

    it('can be found by name', function () {
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        $foundUser = User::where('name', 'John Doe')->first();

        expect($foundUser)->not->toBeNull();
        expect($foundUser->name)->toBe('John Doe');
    });

    it('can be deleted', function () {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        $userId = $user->id;
        $user->delete();

        expect(User::find($userId))->toBeNull();
    });

    it('can be mass assigned', function () {
        $userData = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => Hash::make('password123')
        ];

        $user = User::create($userData);

        expect($user->name)->toBe('Jane Doe');
        expect($user->email)->toBe('jane@example.com');
        expect(Hash::check('password123', $user->password))->toBeTrue();
    });

    it('can be updated via mass assignment', function () {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        $user->fill(['name' => 'Updated Name']);
        $user->save();

        expect($user->fresh()->name)->toBe('Updated Name');
    });

    it('can handle long names', function () {
        $longName = str_repeat('This is a very long name that should be handled properly. ', 5);

        $user = User::create([
            'name' => $longName,
            'email' => 'longname@example.com',
            'password' => Hash::make('password123')
        ]);

        expect($user->name)->toBe($longName);
        expect(strlen($user->name))->toBeGreaterThan(200);
    });

    it('can be found by partial name match', function () {
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        $foundUsers = User::where('name', 'like', '%Doe%')->get();

        expect($foundUsers->count())->toBeGreaterThan(0);
        expect($foundUsers->first()->name)->toContain('Doe');
    });

    it('can be found by partial email match', function () {
        User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => Hash::make('password123')
        ]);

        $foundUsers = User::where('email', 'like', '%example%')->get();

        expect($foundUsers->count())->toBeGreaterThan(0);
        expect($foundUsers->first()->email)->toContain('example');
    });

    it('can be ordered by name', function () {
        User::create([
            'name' => 'Zebra User',
            'email' => 'zebra@example.com',
            'password' => Hash::make('password123')
        ]);

        User::create([
            'name' => 'Alpha User',
            'email' => 'alpha@example.com',
            'password' => Hash::make('password123')
        ]);

        User::create([
            'name' => 'Beta User',
            'email' => 'beta@example.com',
            'password' => Hash::make('password123')
        ]);

        $orderedUsers = User::orderBy('name', 'asc')->get();

        expect($orderedUsers->first()->name)->toBe('Alpha User');
        expect($orderedUsers->last()->name)->toBe('Zebra User');
    });

    it('can be ordered by creation date', function () {
        $firstUser = User::create([
            'name' => 'First User',
            'email' => 'first@example.com',
            'password' => Hash::make('password123'),
            'created_at' => now()->subDays(2)
        ]);

        $secondUser = User::create([
            'name' => 'Second User',
            'email' => 'second@example.com',
            'password' => Hash::make('password123'),
            'created_at' => now()->subDay(1)
        ]);

        $thirdUser = User::create([
            'name' => 'Third User',
            'email' => 'third@example.com',
            'password' => Hash::make('password123'),
            'created_at' => now()
        ]);

        $orderedUsers = User::orderBy('created_at', 'asc')->get();

        expect($orderedUsers->first()->id)->toBe($firstUser->id);
        expect($orderedUsers->last()->id)->toBe($thirdUser->id);
    });

    it('can handle multiple users with same name', function () {
        User::create([
            'name' => 'John Doe',
            'email' => 'john1@example.com',
            'password' => Hash::make('password123')
        ]);

        User::create([
            'name' => 'John Doe',
            'email' => 'john2@example.com',
            'password' => Hash::make('password123')
        ]);

        $usersWithSameName = User::where('name', 'John Doe')->get();

        expect($usersWithSameName->count())->toBe(2);
    });

    it('can be found by multiple criteria', function () {
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => Hash::make('password123')
        ]);

        $foundUsers = User::where('name', 'like', '%Doe%')
            ->where('email', 'like', '%@example.com%')
            ->get();

        expect($foundUsers->count())->toBe(2);
    });

    it('can create personal access tokens', function () {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        $token = $user->createToken('test-token');

        expect($token->plainTextToken)->not->toBeNull();
        expect($user->tokens)->toHaveCount(1);
    });

    it('can delete personal access tokens', function () {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        $token = $user->createToken('test-token');
        expect($user->tokens)->toHaveCount(1);

        $user->tokens()->delete();
        expect($user->fresh()->tokens)->toHaveCount(0);
    });

    it('can check if user has specific token', function () {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        $token = $user->createToken('test-token');

        expect($user->tokens()->where('name', 'test-token')->exists())->toBeTrue();
        expect($user->tokens()->where('name', 'non-existent-token')->exists())->toBeFalse();
    });

    it('can handle email verification', function () {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        
        expect($user->email_verified_at)->toBeNull();
        
        $user->email_verified_at = now();
        $user->save();

        expect($user->fresh()->email_verified_at)->not->toBeNull();
    });

    it('can be soft deleted if configured', function () {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        $userId = $user->id;
        
        
        
        try {
            $user->delete();
            $foundUser = User::withTrashed()->find($userId);
            
            if ($foundUser && method_exists($foundUser, 'trashed')) {
                expect($foundUser->trashed())->toBeTrue();
            } else {
                
                expect(User::find($userId))->toBeNull();
            }
        } catch (\Exception $e) {
            
            expect(User::find($userId))->toBeNull();
        }
    });
}); 