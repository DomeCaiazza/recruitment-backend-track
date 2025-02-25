<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('index returns users list', function () {

    User::factory()->count(3)->create();

    $response = $this->getJson('/api/users?per_page=10');

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            '*' => ['id', 'name', 'surname', 'email']
        ],
        'links',
        'meta',
    ]);
});

test('store creates a new user', function () {
    $payload = [
        'email'    => 'new@example.com',
        'name'     => 'New name',
        'surname'     => 'New surname',
        'password' => 'password123',
    ];

    $response = $this->postJson('/api/users', $payload);

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

    $response = $this->getJson("/api/users/{$user->id}");

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

    $response = $this->putJson("/api/users/{$user->id}", $payload);

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

    $response = $this->deleteJson("/api/users/{$user->id}");

    $response->assertOk();
    $response->assertJsonFragment([
        'message' => 'User deleted successfully.'
    ]);

    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
});
