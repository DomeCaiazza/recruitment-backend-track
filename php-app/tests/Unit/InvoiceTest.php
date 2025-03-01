<?php

use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class);

test('it sets a valid status', function () {
    $invoice = new Invoice([
        'tax_profile_id' => 1,
        'invoice_date'   => now(),
        'subtotal'       => 100,
        'tax_amount'     => 20,
        'discount'       => 10,
        'currency'       => 'EUR',
        'status'         => 'pending',
    ]);

    expect($invoice->status)->toBe('pending');
});

test('it throws exception for invalid status', function () {
    $invoice = new Invoice();
    $invoice->status = 'invalid';
})->throws(InvalidArgumentException::class);

test('it sets a valid currency', function () {
    $invoice = new Invoice();
    $invoice->currency = 'USD';
    expect($invoice->currency)->toBe('USD');
});

test('it throws exception for invalid currency', function () {
    $invoice = new Invoice();
    $invoice->currency = 'GBP';
})->throws(InvalidArgumentException::class);

test('it generates invoice number and calculates total on creating', function () {

    $invoice = new Invoice([
        'invoice_date'   => now(),
        'subtotal'       => 100,
        'tax_amount'     => 20,
        'discount'       => 10,
        'currency'       => 'EUR',
        'status'         => 'paid',
        'paid_at'        => Null,
        'canceled_at'    => Null,
        'notes'          => 'Test invoice'
    ]);

    $invoice->calculateTotal();
    $invoice->setInvoiceNumber();
    $invoice->setStatusTimestampsOnCreate();

    expect($invoice->invoice_number)->not->toBeNull();
    expect($invoice->total)->toBe(110);
    expect($invoice->paid_at)->not->toBeNull();
});

test('it updates total when amounts change', function () {
    $invoice = new Invoice([
        'invoice_date'   => now(),
        'subtotal'       => 100,
        'tax_amount'     => 20,
        'discount'       => 10,
        'currency'       => 'EUR',
        'status'         => 'pending',
    ]);
    $invoice->calculateTotal();
    expect($invoice->total)->toBe(110);

    $invoice->subtotal = 200;
    $invoice->tax_amount = 40;
    $invoice->discount = 20;

    $invoice->calculateTotal();
    expect($invoice->total)->toBe(220);
});

test('it sets paid_at and canceled_at on status update', function () {
    $invoice = new Invoice([
        'invoice_date'   => now(),
        'subtotal'       => 100,
        'tax_amount'     => 20,
        'discount'       => 10,
        'currency'       => 'EUR',
        'status'         => 'pending',
    ]);

    expect($invoice->paid_at)->toBeNull();
    expect($invoice->canceled_at)->toBeNull();

    $invoice->status = 'paid';
    $invoice->updateStatusTimestampsOnUpdate();
    expect($invoice->paid_at)->not->toBeNull();

    $invoice->status = 'canceled';
    $invoice->updateStatusTimestampsOnUpdate();
    expect($invoice->canceled_at)->not->toBeNull();
});