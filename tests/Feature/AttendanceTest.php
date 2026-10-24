<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_kiosk_scanner_page_is_publicly_accessible(): void
    {
        $response = $this->get('/scanner');
        $response->assertStatus(200);
        $response->assertSee('Siglatrolap Kiosk');
    }

    public function test_qr_verification_records_time_in_successfully(): void
    {
        $employee = User::where('role', 'employee')->first();

        // Clear any existing attendance for today to test clean Time-In
        Attendance::where('user_id', $employee->id)->where('date', Carbon::today()->format('Y-m-d'))->delete();

        $response = $this->postJson('/scanner/verify', [
            'qr_secret' => $employee->qr_secret,
            'action' => 'in',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'type' => 'in',
        ]);

        $this->assertNotNull(
            Attendance::where('user_id', $employee->id)
                ->whereDate('date', Carbon::today()->format('Y-m-d'))
                ->first()
        );
    }

    public function test_qr_verification_records_time_out_successfully(): void
    {
        $employee = User::where('role', 'employee')->first();
        $today = Carbon::today()->format('Y-m-d');

        // Setup an attendance that is only checked in
        Attendance::where('user_id', $employee->id)->whereDate('date', $today)->delete();
        Attendance::create([
            'user_id' => $employee->id,
            'date' => $today,
            'check_in' => '07:55:00',
            'check_out' => null,
            'status' => 'On Time',
        ]);

        $response = $this->postJson('/scanner/verify', [
            'qr_secret' => $employee->qr_secret,
            'action' => 'out',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'type' => 'out',
        ]);

        $this->assertNotNull(Attendance::where('user_id', $employee->id)->whereDate('date', $today)->first()->check_out);
    }

    public function test_invalid_qr_secret_returns_404(): void
    {
        $response = $this->postJson('/scanner/verify', [
            'qr_secret' => 'invalid-non-existent-secret-token',
        ]);

        $response->assertStatus(404);
        $response->assertJson(['success' => false]);
    }
}
