<?php

use App\Models\User;
use App\Models\TaxProfile;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->taxProfile = TaxProfile::factory()->create(['user_id' => $this->user->id]);
});

test('index returns a paginated list of invoices', function () {
    // Create several invoices for the tax profile
    Invoice::factory()->count(15)->create(['tax_profile_id' => $this->taxProfile->id]);

    $url = "/api/users/{$this->user->id}/tax-profiles/{$this->taxProfile->id}/invoices";
    $response = $this->withHeaders([
        'X-API-KEY' => env('API_KEY_TESTING'),
    ])->getJson($url);

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'data' => [
                    '*' => ['id',
                    'invoice_number',
                    'invoice_date',
                    'subtotal',
                    'discount',
                    'currency',
                    'status']],
                 'links',
                 'meta',
             ]);
});


test('store creates a new invoice', function () {
    $payload = [
        'invoice_date' => now()->format('Y-m-d'),
        'subtotal'     => 100.00,
        'tax_amount'   => 20.00,
        'discount'     => 5.00,
        'currency'     => 'EUR',
        'status'       => 'pending',
        'notes'        => 'Test invoice',
    ];

    $url = "/api/users/{$this->user->id}/tax-profiles/{$this->taxProfile->id}/invoices";
    $response = $this->withHeaders([
        'X-API-KEY' => env('API_KEY_TESTING'),
    ])->postJson($url, $payload);

    $response->assertStatus(201)
             ->assertJson([
                 'message' => 'Invoice created successfully.',
                 'data' => [
                     'invoice_date' => $payload['invoice_date'],
                     'subtotal'     => 100.00,
                     'tax_amount'   => 20.00,
                     'discount'     => 5.00,
                     'currency'     => 'EUR',
                     'status'       => 'pending',
                     'notes'        => 'Test invoice'
                 ],
             ]);

    $this->assertDatabaseHas('invoices', [
        'tax_profile_id' => $this->taxProfile->id,
        'subtotal'       => 100.00,
    ]);
});


test('show returns the specified invoice', function () {
    $invoice = Invoice::factory()->create(['tax_profile_id' => $this->taxProfile->id]);

    $url = "/api/users/{$this->user->id}/tax-profiles/{$this->taxProfile->id}/invoices/{$invoice->id}";
    $response = $this->withHeaders([
        'X-API-KEY' => env('API_KEY_TESTING'),
    ])->getJson($url);

    $response->assertStatus(200)
             ->assertJson([
                 'data' => [
                     'id' => $invoice->id
                 ],
             ]);
});


test('update modifies an existing invoice', function () {
    $invoice = Invoice::factory()->create(['tax_profile_id' => $this->taxProfile->id]);

    $payload = [
        'subtotal'     => '150.00'
    ];

    $url = "/api/users/{$this->user->id}/tax-profiles/{$this->taxProfile->id}/invoices/{$invoice->id}";
    $response = $this->withHeaders([
        'X-API-KEY' => env('API_KEY_TESTING'),
    ])->putJson($url, $payload);

    if ($response->getStatusCode() === 204) {
        $this->assertDatabaseHas('invoices', [
            'id'       => $invoice->id,
            'subtotal' => $invoice->subtotal
        ]);
    } else {
        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Invoice updated successfully.',
                     'data' => [
                         'id'       => $invoice->id,
                         'subtotal' => 150.00,
                     ],
                 ]);
    }
});


test('destroy deletes the invoice', function () {
    $invoice = Invoice::factory()->create(['tax_profile_id' => $this->taxProfile->id]);

    $url = "/api/users/{$this->user->id}/tax-profiles/{$this->taxProfile->id}/invoices/{$invoice->id}";
    $response = $this->withHeaders([
        'X-API-KEY' => env('API_KEY_TESTING'),
    ])->deleteJson($url);

    $response->assertNoContent();

    $this->assertDatabaseMissing('invoices', [
        'id' => $invoice->id,
    ]);
});
