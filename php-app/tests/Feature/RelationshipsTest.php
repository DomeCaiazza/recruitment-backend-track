<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\TaxProfile;
use App\Models\Invoice;

class RelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_tax_profile_invoice_relationship()
    {
        $user = User::factory()->create();
        $taxProfile = TaxProfile::factory()->create([
            'user_id' => $user->id,
        ]);
        $invoice = Invoice::factory()->create([
            'tax_profile_id' => $taxProfile->id,
        ]);

        $this->assertNotNull($user->taxProfiles);
        $this->assertInstanceOf(TaxProfile::class, $user->taxProfiles->first());
        $this->assertEquals($taxProfile->id, $user->taxProfiles->first()->id);

        $this->assertNotNull($taxProfile->invoices);
        $this->assertInstanceOf(Invoice::class, $taxProfile->invoices->first());
        $this->assertEquals($invoice->id, $taxProfile->invoices->first()->id);
    }
}
