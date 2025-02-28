<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('index returns users list', function () {

    User::factory()->count(3)->create();

    $response = $this->withHeaders([
        'X-API-KEY' => env('API_KEY_TESTING'),
    ])->getJson('/api/users?per_page=10');

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            '*' => ['id', 'name', 'surname', 'email']
        ],
        'links',
        'meta',
    ]);
});


test('filters users by surname', function () {
    $user1 = User::factory()->create(['surname' => 'ABC123']);
    $user2 = User::factory()->create(['surname' => 'DEF123']);

    $response = $this->withHeaders([
        'X-API-KEY' => env('API_KEY_TESTING'),
    ])->getJson(route('users.index', [
        'filter[surname]' => 'ABC'
    ]));

    $response->assertOk()
             ->assertJsonFragment(['surname' => 'ABC123'])
             ->assertJsonMissing(['surname' => 'XYZ456']);
});

test('store creates a new user', function () {
    $payload = [
        'email'    => 'new@example.com',
        'name'     => 'New name',
        'surname'     => 'New surname',
        'password' => 'password123',
    ];

    $response = $this->withHeaders([
        'X-API-KEY' => env('API_KEY_TESTING'),
    ])->postJson('/api/users', $payload);

    $response->assertStatus(201);
    $response->assertJsonFragment([
        'email' => 'new@example.com',
        'name'  => 'New name',
        'surname'  => 'New surname',
    ]);

    $this->assertDatabaseHas('users', [
        'email' => 'new@example.com',
    ]);
});

test('show returns a specific user', function () {
    $user = User::factory()->create();

    $response = $this->withHeaders([
        'X-API-KEY' => env('API_KEY_TESTING'),
    ])->getJson("/api/users/{$user->id}");

    $response->assertOk();
    $response->assertJsonFragment([
        'email' => $user->email,
        'name'  => $user->name,
        'surname'  => $user->surname,
    ]);
});

test('update modifies an existing user', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
    ]);

    $payload = [
        'name' => 'Updated Name',
    ];

    $response = $this->withHeaders([
        'X-API-KEY' => env('API_KEY_TESTING'),
    ])->putJson("/api/users/{$user->id}", $payload);

    $response->assertOk();
    $response->assertJsonFragment([
        'name' => 'Updated Name',
    ]);

    $this->assertDatabaseHas('users', [
        'id'   => $user->id,
        'name' => 'Updated Name',
    ]);
});

test('destroy deletes a user', function () {
    $user = User::factory()->create();

    $response = $this->withHeaders([
        'X-API-KEY' => env('API_KEY_TESTING'),
    ])->deleteJson("/api/users/{$user->id}");

    $response->assertNoContent();
    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
});
