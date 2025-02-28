<?php

namespace Database\Factories;


use App\Models\Invoice;
use App\Models\TaxProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 1, 100);
        return [
            'tax_profile_id' => TaxProfile::factory(),
            'invoice_date' => now(),
            'subtotal' => $subtotal,
            'tax_amount' => $subtotal * 0.22,
            'currency' => 'EUR',
            'status' => 'pending',
        ];
    }
}
