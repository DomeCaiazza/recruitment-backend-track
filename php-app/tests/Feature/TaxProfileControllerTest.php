<?php

use App\Models\User;
use App\Models\TaxProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('returns a list of tax profiles', function () {
    TaxProfile::factory()->count(5)->create(['user_id' => $this->user->id]);

    $response = $this->withHeaders([
        'X-API-KEY' => 'secret_testing',
    ])->getJson(route('users.tax-profiles.index', ['user' => $this->user->id]) . '?per_page=10');

    $response->assertOk()
             ->assertJsonStructure([
                 'data' => [
                     '*' => ['id',
                     'tax_code',
                     'address',
                     'vat_number',
                     'business_name']
                 ]
             ]);
});

test('filters tax profiles by tax code', function () {
    $profile1 = TaxProfile::factory()->create(['user_id' => $this->user->id, 'tax_code' => 'ABC123']);
    $profile2 = TaxProfile::factory()->create(['user_id' => $this->user->id, 'tax_code' => 'XYZ456']);

    $response = $this->withHeaders([
        'X-API-KEY' => 'secret_testing',
    ])->getJson(route('users.tax-profiles.index', [
        'user' => $this->user->id,
        'filter[tax_code]' => 'ABC'
    ]));

    $response->assertOk()
             ->assertJsonFragment(['tax_code' => 'ABC123'])
             ->assertJsonMissing(['tax_code' => 'XYZ456']);
});

test('creates a new tax profile', function () {
    $data = [
        'tax_code' => '123456789',
        'address' => 'Test Address',
        'vat_number' => 'VAT123456',
        'business_name' => 'Test Business'
    ];

    $response = $this->withHeaders([
        'X-API-KEY' => 'secret_testing',
    ])->postJson(route('users.tax-profiles.store', $this->user), $data);

    $response->assertCreated()
             ->assertJsonFragment(['tax_code' => '123456789']);

    $this->assertDatabaseHas('tax_profiles', ['tax_code' => '123456789']);
});

test('displays a specific tax profile', function () {
    $taxProfile = TaxProfile::factory()->create(['user_id' => $this->user->id]);

    $response = $this->withHeaders([
        'X-API-KEY' => 'secret_testing',
    ])->getJson(route('users.tax-profiles.show', [$this->user, $taxProfile]));

    $response->assertOk()
             ->assertJsonFragment(['id' => $taxProfile->id]);
});

test('returns 404 if user does not own tax profile', function () {
    $anotherUser = User::factory()->create();
    $taxProfile = TaxProfile::factory()->create(['user_id' => $anotherUser->id]);

    $response = $this->withHeaders([
        'X-API-KEY' => 'secret_testing',
    ])->getJson(route('users.tax-profiles.show', [$this->user, $taxProfile]));

    $response->assertNotFound();
});

test('updates a tax profile', function () {
    $taxProfile = TaxProfile::factory()->create(['user_id' => $this->user->id]);

    $updateData = ['business_name' => 'Updated Business'];

    $response = $this->withHeaders([
        'X-API-KEY' => 'secret_testing',
    ])->putJson(route('users.tax-profiles.update', [$this->user, $taxProfile]), $updateData);

    $response->assertOk()
             ->assertJsonFragment(['business_name' => 'Updated Business']);

    $this->assertDatabaseHas('tax_profiles', ['id' => $taxProfile->id, 'business_name' => 'Updated Business']);
});

test('deletes a tax profile', function () {
    $taxProfile = TaxProfile::factory()->create(['user_id' => $this->user->id]);

    $response = $this->withHeaders([
        'X-API-KEY' => 'secret_testing',
    ])->deleteJson(route('users.tax-profiles.destroy', [$this->user, $taxProfile]));

    $response->assertNoContent();

    $this->assertDatabaseMissing('tax_profiles', ['id' => $taxProfile->id]);
});
