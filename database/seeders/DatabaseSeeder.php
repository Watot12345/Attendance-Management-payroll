<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\CompanySetting;
use App\Models\Leave;
use App\Models\Payroll;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Company Settings
        CompanySetting::set('company_name', 'Siglatrolap Attendance & Payroll Management', 'Company Display Name');
        CompanySetting::set('company_email', 'hr@siglatrolap.com', 'Company Contact Email');
        CompanySetting::set('company_phone', '+63 912 345 6789', 'Company Contact Phone');
        CompanySetting::set('company_address', 'Metro Manila, Philippines', 'Office Address');
        CompanySetting::set('work_start_time', '08:00:00', 'Official work start time');
        CompanySetting::set('work_end_time', '17:00:00', 'Official work end time');
        CompanySetting::set('regular_hours_per_day', '8', 'Regular work hours per day');
        CompanySetting::set('overtime_rate_30m', '70', 'Overtime pay rate per 30 minutes block (PHP)');
        CompanySetting::set('late_penalty_30m', '300', 'Deduction penalty per 30 minutes late (PHP)');
        CompanySetting::set('sss_monthly_deduction', '300', 'Standard monthly SSS deduction (PHP)');
        CompanySetting::set('insurance_monthly_deduction', '300', 'Standard monthly Health/Insurance deduction (PHP)');

        // 2. Seed Admin Users
        $admin = User::create([
            'name' => 'System Administrator',
            'username' => 'admin',
            'email' => 'admin@siglatrolap.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'qr_secret' => 'admin-qr-token-key-2026',
            'phone' => '09170001111',
            'department' => 'Executive Office',
            'position' => 'Senior HR Director',
            'basic_salary' => 75000.00,
            'daily_rate' => 3409.09,
            'hourly_rate' => 426.14,
            'status' => 'active',
            'hire_date' => Carbon::now()->subYears(3)->format('Y-m-d'),
        ]);

        $legacyAdmin = User::create([
            'name' => 'Legacy Admin',
            'username' => 'adminer',
            'email' => 'dmins@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'qr_secret' => 'legacy-admin-qr-secret',
            'phone' => '09170002222',
            'department' => 'Operations',
            'position' => 'Operations Manager',
            'basic_salary' => 60000.00,
            'daily_rate' => 2727.27,
            'hourly_rate' => 340.91,
            'status' => 'active',
            'hire_date' => Carbon::now()->subYears(2)->format('Y-m-d'),
        ]);

        // 3. Seed Employees
        $employeesData = [
            [
                'name' => 'Joshua Sierra',
                'username' => 'joshuasierra',
                'email' => 'joshua@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'qr_secret' => 'a54f2eb482117b65406a167087b92aba',
                'phone' => '09181112233',
                'department' => 'Engineering',
                'position' => 'Senior Full Stack Developer',
                'basic_salary' => 55000.00,
                'daily_rate' => 2500.00,
                'hourly_rate' => 312.50,
                'status' => 'active',
                'avatar' => 'SierraProf.jpg',
                'hire_date' => Carbon::now()->subMonths(18)->format('Y-m-d'),
            ],
            [
                'name' => 'Joshua Arncel',
                'username' => 'josh',
                'email' => 'joshuasierra725@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'qr_secret' => '8015032ca6edfe3928707b1a948523fd',
                'phone' => '09182223344',
                'department' => 'Product Design',
                'position' => 'Lead UI/UX Designer',
                'basic_salary' => 48000.00,
                'daily_rate' => 2181.82,
                'hourly_rate' => 272.73,
                'status' => 'active',
                'avatar' => 'profile.jpg',
                'hire_date' => Carbon::now()->subMonths(12)->format('Y-m-d'),
            ],
            [
                'name' => 'Maria Santos',
                'username' => 'msantos',
                'email' => 'maria.santos@siglatrolap.com',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'qr_secret' => 'cf16a683c0ad35e59fdd283464c139af',
                'phone' => '09183334455',
                'department' => 'Finance & Accounting',
                'position' => 'Payroll Specialist',
                'basic_salary' => 45000.00,
                'daily_rate' => 2045.45,
                'hourly_rate' => 255.68,
                'status' => 'active',
                'avatar' => 'GirayProf.jpg',
                'hire_date' => Carbon::now()->subMonths(8)->format('Y-m-d'),
            ],
            [
                'name' => 'Juan Dela Cruz',
                'username' => 'jdelacruz',
                'email' => 'juan.delacruz@siglatrolap.com',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'qr_secret' => '699d5cefcb393211544b96879527b9d2',
                'phone' => '09184445566',
                'department' => 'Operations',
                'position' => 'Quality Assurance Analyst',
                'basic_salary' => 42000.00,
                'daily_rate' => 1909.09,
                'hourly_rate' => 238.64,
                'status' => 'active',
                'avatar' => 'LapizProf.png',
                'hire_date' => Carbon::now()->subMonths(6)->format('Y-m-d'),
            ],
        ];

        $employees = [];
        foreach ($employeesData as $empData) {
            $employees[] = User::create($empData);
        }

        // 4. Seed Attendance History for past 20 days
        $today = Carbon::today();
        foreach ($employees as $index => $employee) {
            for ($d = 20; $d >= 0; $d--) {
                $date = $today->copy()->subDays($d);
                if ($date->isWeekend()) {
                    continue; // Skip weekends
                }

                // Variation in attendance
                $randomFactor = ($d + $index) % 5;
                if ($d === 0) {
                    // Today: some clocked in, some not yet
                    if ($index === 0) {
                        Attendance::create([
                            'user_id' => $employee->id,
                            'date' => $date->format('Y-m-d'),
                            'check_in' => '07:52:00',
                            'check_out' => null,
                            'status' => 'On Time',
                            'late_minutes' => 0,
                            'overtime_minutes' => 0,
                            'work_hours' => 0,
                            'scan_method' => 'qr',
                            'notes' => 'Clocked in via QR Scanner',
                        ]);
                    } elseif ($index === 1) {
                        Attendance::create([
                            'user_id' => $employee->id,
                            'date' => $date->format('Y-m-d'),
                            'check_in' => '08:24:00',
                            'check_out' => null,
                            'status' => 'Late',
                            'late_minutes' => 24,
                            'overtime_minutes' => 0,
                            'work_hours' => 0,
                            'scan_method' => 'qr',
                            'notes' => 'Late arrival due to transit delay',
                        ]);
                    }
                    continue;
                }

                $checkIn = '07:55:00';
                $checkOut = '17:05:00';
                $status = 'On Time';
                $lateMinutes = 0;
                $otMinutes = 0;
                $workHours = 8.00;

                if ($randomFactor === 1) {
                    // Late day
                    $checkIn = '08:35:00';
                    $checkOut = '17:30:00';
                    $status = 'Late';
                    $lateMinutes = 35;
                    $workHours = 7.50;
                } elseif ($randomFactor === 2) {
                    // Overtime day
                    $checkIn = '07:50:00';
                    $checkOut = '19:15:00';
                    $status = 'Overtime';
                    $otMinutes = 135;
                    $workHours = 10.25;
                } elseif ($randomFactor === 3) {
                    // Standard smooth day
                    $checkIn = '07:58:00';
                    $checkOut = '17:00:00';
                    $status = 'On Time';
                    $workHours = 8.00;
                }

                Attendance::create([
                    'user_id' => $employee->id,
                    'date' => $date->format('Y-m-d'),
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'status' => $status,
                    'late_minutes' => $lateMinutes,
                    'overtime_minutes' => $otMinutes,
                    'work_hours' => $workHours,
                    'scan_method' => 'qr',
                    'notes' => 'Verified biometric QR attendance log',
                ]);
            }
        }

        // 5. Seed Leave Requests
        Leave::create([
            'user_id' => $employees[0]->id,
            'leave_type' => 'Vacation Leave',
            'start_date' => Carbon::today()->addDays(5)->format('Y-m-d'),
            'end_date' => Carbon::today()->addDays(7)->format('Y-m-d'),
            'days_count' => 3,
            'reason' => 'Annual family gathering and vacation in Palawan.',
            'status' => 'pending',
        ]);

        Leave::create([
            'user_id' => $employees[1]->id,
            'leave_type' => 'Sick Leave',
            'start_date' => Carbon::today()->subDays(6)->format('Y-m-d'),
            'end_date' => Carbon::today()->subDays(5)->format('Y-m-d'),
            'days_count' => 2,
            'reason' => 'Acute gastroenteritis, medical certificate attached.',
            'status' => 'approved',
            'admin_remarks' => 'Approved. Take care and get well soon!',
            'reviewed_by' => $admin->id,
            'reviewed_at' => Carbon::today()->subDays(5),
        ]);

        Leave::create([
            'user_id' => $employees[2]->id,
            'leave_type' => 'Emergency Leave',
            'start_date' => Carbon::today()->subDays(12)->format('Y-m-d'),
            'end_date' => Carbon::today()->subDays(12)->format('Y-m-d'),
            'days_count' => 1,
            'reason' => 'Urgent home pipe leak and electrical maintenance.',
            'status' => 'approved',
            'admin_remarks' => 'Approved on short notice.',
            'reviewed_by' => $admin->id,
            'reviewed_at' => Carbon::today()->subDays(12),
        ]);

        Leave::create([
            'user_id' => $employees[3]->id,
            'leave_type' => 'Vacation Leave',
            'start_date' => Carbon::today()->addDays(15)->format('Y-m-d'),
            'end_date' => Carbon::today()->addDays(20)->format('Y-m-d'),
            'days_count' => 6,
            'reason' => 'Personal personal retreat and rest.',
            'status' => 'pending',
        ]);

        // 6. Seed Payroll for each employee (Previous month & current month draft)
        $lastMonthStart = Carbon::today()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::today()->subMonth()->endOfMonth();

        foreach ($employees as $employee) {
            $basic = (float) $employee->basic_salary;
            $daily = (float) $employee->daily_rate;
            $hourly = (float) $employee->hourly_rate;

            $totalRegularHours = 176.00;
            $attendancePay = round($totalRegularHours * $hourly, 2);
            $otHours = 12.00;
            $otPay = round(($otHours * 2) * 70, 2); // 70 pesos per 30 mins
            $lateMins = 45;
            $lateDeduction = round(floor($lateMins / 30) * 300, 2);
            $sss = 300.00;
            $insurance = 300.00;
            $tax = round($basic * 0.05, 2); // 5% withholding tax estimate
            $gross = $basic + $otPay;
            $totalDeductions = $lateDeduction + $sss + $insurance + $tax;
            $net = $gross - $totalDeductions;

            Payroll::create([
                'user_id' => $employee->id,
                'period_start' => $lastMonthStart->format('Y-m-d'),
                'period_end' => $lastMonthEnd->format('Y-m-d'),
                'basic_salary' => $basic,
                'days_worked' => 22,
                'regular_hours' => $totalRegularHours,
                'hourly_rate' => $hourly,
                'attendance_pay' => $attendancePay,
                'overtime_hours' => $otHours,
                'overtime_pay' => $otPay,
                'late_minutes' => $lateMins,
                'late_deduction' => $lateDeduction,
                'sss_deduction' => $sss,
                'insurance_deduction' => $insurance,
                'tax_deduction' => $tax,
                'other_deductions' => 0.00,
                'gross_pay' => $gross,
                'total_deductions' => $totalDeductions,
                'net_pay' => $net,
                'status' => 'paid',
                'payment_method' => 'Direct Bank Deposit',
                'payment_date' => $lastMonthEnd->format('Y-m-d'),
                'notes' => 'Successfully disbursed via automated ACH bank transfer.',
            ]);
        }
    }
}
