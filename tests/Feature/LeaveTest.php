<?php

namespace Tests\Feature;

use App\Models\Leave;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_employee_can_submit_leave_application(): void
    {
        $employee = User::where('role', 'employee')->first();
        $start = Carbon::today()->addDays(2)->format('Y-m-d');
        $end = Carbon::today()->addDays(4)->format('Y-m-d');

        $response = $this->actingAs($employee)->post('/leaves', [
            'leave_type' => 'Vacation Leave',
            'start_date' => $start,
            'end_date' => $end,
            'reason' => 'Annual vacation break with family.',
        ]);

        $response->assertSessionHas('success');
        $this->assertTrue(Leave::where('user_id', $employee->id)->where('status', 'pending')->count() > 0);
    }

    public function test_admin_can_approve_leave(): void
    {
        $admin = User::where('role', 'admin')->first();
        $leave = Leave::where('status', 'pending')->first();

        $response = $this->actingAs($admin)->patch("/leaves/{$leave->id}/status", [
            'status' => 'approved',
            'admin_remarks' => 'Approved by Director.',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('approved', $leave->fresh()->status);
        $this->assertEquals($admin->id, $leave->fresh()->reviewed_by);
    }
}
