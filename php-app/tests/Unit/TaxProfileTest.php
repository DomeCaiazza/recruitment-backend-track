<?php

use App\Models\TaxProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it creates a valid TaxProfile', function () {
    $data = [
        'user_id'      => 1,
        'tax_code'     => 'ABC123',
        'address'      => 'Via Test 1',
        'vat_number'   => 'VAT123',
        'business_name'=> 'Test Business'
    ];

    $taxProfile = new TaxProfile($data);

    expect($taxProfile->user_id)->toBe(1);
    expect($taxProfile->tax_code)->toBe('ABC123');
    expect($taxProfile->vat_number)->toBe('VAT123');
});