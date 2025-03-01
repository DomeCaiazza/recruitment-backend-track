<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('it creates a valid User', function () {
    $data = [
        'name' => 'Test',
        'surname' => 'User',
        'email' => 'test@email.it',
        'password' => 'password',
    ];

    $user = new User($data);

    expect($user->name)->toBe('Test');
    expect($user->surname)->toBe('User');
    expect($user->email)->toBe('test@email.it');
    expect(Hash::check('password', $user->password))->toBeTrue();
});