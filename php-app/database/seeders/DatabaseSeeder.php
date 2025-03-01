<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TaxProfile;
use App\Models\Invoice;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $multiple_tax_profiles = TaxProfile::factory()->count(5)->create(['user_id' => $user1->id]);
        $single_tax_profile = TaxProfile::factory()->create(['user_id' => $user2->id]);

        Invoice::factory()->count(5)->create(['tax_profile_id' =>$multiple_tax_profiles->first()->id]);
        Invoice::factory()->count(2)->create(['tax_profile_id' =>$single_tax_profile->id]);
    }
}
