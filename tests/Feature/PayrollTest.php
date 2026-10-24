<?php

namespace Tests\Feature;

use App\Models\Payroll;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_generate_payroll_for_period(): void
    {
        $admin = User::where('role', 'admin')->first();
        $start = Carbon::today()->startOfMonth()->format('Y-m-d');
        $end = Carbon::today()->endOfMonth()->format('Y-m-d');

        $response = $this->actingAs($admin)->post('/payrolls/generate', [
            'period_start' => $start,
            'period_end' => $end,
        ]);

        $response->assertSessionHas('success');
        $this->assertTrue(Payroll::whereDate('period_start', $start)->whereDate('period_end', $end)->count() > 0);
    }

    public function test_employee_can_view_their_own_payslip(): void
    {
        $payroll = Payroll::first();
        $employee = $payroll->user;

        $response = $this->actingAs($employee)->get("/payrolls/{$payroll->id}");
        $response->assertStatus(200);
        $response->assertSee('Official Employee Payslip');
    }

    public function test_employee_cannot_view_another_employees_payslip(): void
    {
        $payroll = Payroll::first();
        $otherEmployee = User::where('role', 'employee')->where('id', '!=', $payroll->user_id)->first();

        $response = $this->actingAs($otherEmployee)->get("/payrolls/{$payroll->id}");
        $response->assertStatus(403);
    }
}
