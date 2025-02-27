<?php

namespace Database\Factories;


use App\Models\TaxProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaxProfile>
 */
class TaxProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'tax_code' => strtoupper(fake()->bothify('?????????????')),
            'vat_number' => fake()->numerify('IT###########'),
            'business_name' => fake()->company(),
            'address' => fake()->address(),
        ];
    }
}
