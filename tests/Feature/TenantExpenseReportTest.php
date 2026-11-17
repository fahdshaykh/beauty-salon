<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Expense;
use App\ExpenseType;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TenantExpenseReportTest extends TestCase
{
    use DatabaseTransactions;

    protected function getAuthorizedUser(): User
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user available.');
        }

        setPermissionsTeamId($user->company_id);

        return $user;
    }

    public function test_tenant_expense_reports_index_loads_for_authorized_user()
    {
        $user = $this->getAuthorizedUser();

        $response = $this->actingAs($user)->get(route('expense.reports'));
        $response->assertStatus(200);
        $response->assertViewIs('reports.expense_index');
        $response->assertSee('Expense Report');
        $response->assertSee('Select Expense Type');
        $response->assertDontSee('$.ajax');
    }

    public function test_tenant_expense_record_validation_requires_expense_type()
    {
        $user = $this->getAuthorizedUser();

        $response = $this->actingAs($user)
            ->from(route('expense.reports'))
            ->get(route('expense.record', [
                'expense_type' => '',
            ]));

        $response->assertStatus(302);
        $response->assertRedirect(route('expense.reports'));
        $response->assertSessionHasErrors(['expense_type']);
    }

    public function test_tenant_expense_record_returns_full_view_not_json()
    {
        $user = $this->getAuthorizedUser();

        $expenseType = ExpenseType::where('company_id', $user->company_id)->first();
        if (!$expenseType) {
            $expenseType = ExpenseType::create([
                'type'       => 'Office Supplies',
                'company_id' => $user->company_id,
            ]);
        }

        $paymentMethod = DB::table('payment_methods')->first();

        // Create a test expense record
        Expense::create([
            'company_id'     => $user->company_id,
            'expense_type'   => $expenseType->id,
            'payment_method' => $paymentMethod ? $paymentMethod->id : 1,
            'amount'         => 85.00,
            'payable'        => date('Y-m-d'),
        ]);

        $response = $this->actingAs($user)->get(route('expense.record', [
            'expense_type' => $expenseType->id,
            'start_date'   => date('Y-m-01'),
            'end_date'     => date('Y-m-d'),
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('reports.expense_reports');
        $response->assertViewHas('expense_report');
        $response->assertSee('Expense Report');
        $response->assertSee('Grand Total:');
        $response->assertSee('85.00');
        $response->assertSee('Back to Filters');
        $response->assertSee('Export Expense Report');
    }

    public function test_tenant_expense_export_streams_csv()
    {
        $user = $this->getAuthorizedUser();

        $response = $this->actingAs($user)->get(route('expense.export', [
            'start_date'   => 'no_start_date',
            'end_date'     => 'no_end_date',
            'expense_type' => 'no',
            'method'       => 'no',
        ]));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
    }
}
