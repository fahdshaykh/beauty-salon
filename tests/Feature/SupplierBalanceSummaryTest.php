<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\InventoryPurchases;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierBalanceSummaryTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $supplier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::first();
        if ($this->user) {
            $this->supplier = Supplier::where('company_id', $this->user->company_id)->first();
        }
    }

    public function test_supplier_balance_summary_page_loads_successfully()
    {
        if (!$this->user || !$this->supplier) {
            $this->markTestSkipped('No user or supplier available.');
        }

        $response = $this->actingAs($this->user)->get(route('suppliers.balance_summary', $this->supplier->id));

        $response->assertStatus(200);
        $response->assertSee($this->supplier->name);
        $response->assertSee('Statement of Account');
        $response->assertSee('Opening Balance');
        $response->assertSee('Total Purchases');
        $response->assertSee('Total Paid');
        $response->assertSee('Net Payable');
        $response->assertSee('Unified Supplier Ledger');
        $response->assertSee('Purchase Invoices', false);
        $response->assertSee('Payment Receipts', false);
    }

    public function test_supplier_balance_summary_with_date_range()
    {
        if (!$this->user || !$this->supplier) {
            $this->markTestSkipped('No user or supplier available.');
        }

        $response = $this->actingAs($this->user)->get(route('suppliers.balance_summary', [
            'id' => $this->supplier->id,
            'start_date' => '2026-01-01',
            'end_date'   => '2026-12-31',
        ]));

        $response->assertStatus(200);
        $response->assertSee('2026-01-01');
        $response->assertSee('2026-12-31');
    }

    public function test_supplier_balance_summary_export_csv()
    {
        if (!$this->user || !$this->supplier) {
            $this->markTestSkipped('No user or supplier available.');
        }

        $response = $this->actingAs($this->user)->get(route('suppliers.balance_summary.export', [
            'id' => $this->supplier->id,
            'start_date' => '2026-01-01',
            'end_date'   => '2026-12-31',
        ]));

        $response->assertStatus(200);
        $this->assertTrue(
            str_contains($response->headers->get('content-type'), 'text/csv') || 
            str_contains($response->headers->get('content-disposition'), 'attachment')
        );
    }

    public function test_cross_tenant_supplier_balance_summary_is_forbidden()
    {
        if (!$this->user) {
            $this->markTestSkipped('No user available.');
        }

        // Find or create a supplier in a different company
        $otherSupplier = Supplier::where('company_id', '!=', $this->user->company_id)->first();
        if (!$otherSupplier) {
            $otherSupplier = Supplier::create([
                'company_id' => 9999,
                'name' => 'Foreign Supplier Corp',
            ]);
        }

        $response = $this->actingAs($this->user)->get(route('suppliers.balance_summary', $otherSupplier->id));
        $this->assertEquals(404, $response->getStatusCode());
    }
}
