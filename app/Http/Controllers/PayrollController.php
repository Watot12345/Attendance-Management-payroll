<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\CompanySetting;
use App\Models\Payroll;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Payroll::with('user');

        if ($user->isEmployee()) {
            $query->where('user_id', $user->id);
        } elseif ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->filled('period')) {
            $period = $request->input('period');
            $query->whereYear('period_end', substr($period, 0, 4))
                  ->whereMonth('period_end', substr($period, 5, 2));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $payrolls = $query->latest('period_end')->paginate(15)->withQueryString();
        $employees = $user->isAdmin() ? User::where('role', 'employee')->orderBy('name')->get() : collect();

        $totalDisbursed = $user->isAdmin()
            ? Payroll::where('status', 'paid')->sum('net_pay')
            : Payroll::where('user_id', $user->id)->where('status', 'paid')->sum('net_pay');

        return view('payroll.index', compact('payrolls', 'employees', 'totalDisbursed'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        $startDate = $validated['period_start'];
        $endDate = $validated['period_end'];

        $employeesQuery = User::where('role', 'employee')->where('status', 'active');
        if (!empty($validated['user_id'])) {
            $employeesQuery->where('id', $validated['user_id']);
        }
        $employees = $employeesQuery->get();

        $otRatePer30m = (float) CompanySetting::get('overtime_rate_30m', 70);
        $latePenalty30m = (float) CompanySetting::get('late_penalty_30m', 300);
        $sssDeduction = (float) CompanySetting::get('sss_monthly_deduction', 300);
        $insuranceDeduction = (float) CompanySetting::get('insurance_monthly_deduction', 300);

        $processedCount = 0;

        foreach ($employees as $employee) {
            $attendances = Attendance::where('user_id', $employee->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            $daysWorked = $attendances->count();
            $totalRegularHours = 0;
            $totalOvertimeMinutes = 0;
            $totalLateMinutes = 0;

            foreach ($attendances as $att) {
                $hours = (float) $att->work_hours;
                $regular = min($hours, 8.00);
                $totalRegularHours += $regular;
                $totalOvertimeMinutes += $att->overtime_minutes;
                $totalLateMinutes += $att->late_minutes;
            }

            $basicSalary = (float) $employee->basic_salary;
            $hourlyRate = (float) $employee->hourly_rate;
            if ($hourlyRate <= 0) {
                $hourlyRate = round(($basicSalary / 22) / 8, 2);
            }

            $attendancePay = round($totalRegularHours * $hourlyRate, 2);

            $otBlocks = ceil($totalOvertimeMinutes / 30);
            $overtimePay = round($otBlocks * $otRatePer30m, 2);
            $overtimeHours = round($totalOvertimeMinutes / 60, 2);

            $lateBlocks = floor($totalLateMinutes / 30);
            $lateDeduction = min($lateBlocks * $latePenalty30m, 3000); // capped at 3000

            $grossPay = $basicSalary + $overtimePay;
            $taxDeduction = round(max(0, ($grossPay - 20833) * 0.15), 2); // Progressive approx tax
            $totalDeductions = $lateDeduction + $sssDeduction + $insuranceDeduction + $taxDeduction;
            $netPay = max(0, $grossPay - $totalDeductions);

            $existingPayroll = Payroll::where('user_id', $employee->id)
                ->whereDate('period_start', $startDate)
                ->whereDate('period_end', $endDate)
                ->first();

            $payrollData = [
                'user_id' => $employee->id,
                'period_start' => $startDate,
                'period_end' => $endDate,
                'basic_salary' => $basicSalary,
                'days_worked' => $daysWorked,
                'regular_hours' => $totalRegularHours,
                'hourly_rate' => $hourlyRate,
                'attendance_pay' => $attendancePay,
                'overtime_hours' => $overtimeHours,
                'overtime_pay' => $overtimePay,
                'late_minutes' => $totalLateMinutes,
                'late_deduction' => $lateDeduction,
                'sss_deduction' => $sssDeduction,
                'insurance_deduction' => $insuranceDeduction,
                'tax_deduction' => $taxDeduction,
                'other_deductions' => 0.00,
                'gross_pay' => $grossPay,
                'total_deductions' => $totalDeductions,
                'net_pay' => $netPay,
                'status' => 'processed',
                'notes' => 'Generated automatically from attendance logs.',
            ];

            if ($existingPayroll) {
                $existingPayroll->update($payrollData);
            } else {
                Payroll::create($payrollData);
            }

            $processedCount++;
        }

        return back()->with('success', "Payroll calculation completed successfully for {$processedCount} employee(s).");
    }

    public function show(Payroll $payroll)
    {
        $user = Auth::user();
        if ($user->isEmployee() && $payroll->user_id !== $user->id) {
            abort(403);
        }

        $attendances = Attendance::where('user_id', $payroll->user_id)
            ->whereBetween('date', [$payroll->period_start, $payroll->period_end])
            ->orderBy('date')
            ->get();

        return view('payroll.show', compact('payroll', 'attendances'));
    }

    public function print(Payroll $payroll)
    {
        $user = Auth::user();
        if ($user->isEmployee() && $payroll->user_id !== $user->id) {
            abort(403);
        }

        $company = [
            'name' => CompanySetting::get('company_name', 'Siglatrolap Payroll Services'),
            'address' => CompanySetting::get('company_address', 'Metro Manila, Philippines'),
            'phone' => CompanySetting::get('company_phone', '+63 912 345 6789'),
            'email' => CompanySetting::get('company_email', 'payroll@siglatrolap.com'),
        ];

        return view('payroll.print', compact('payroll', 'company'));
    }

    public function updateStatus(Request $request, Payroll $payroll)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:draft,processed,paid'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'payment_date' => ['nullable', 'date'],
        ]);

        $payroll->update([
            'status' => $validated['status'],
            'payment_method' => $validated['payment_method'] ?? $payroll->payment_method,
            'payment_date' => $validated['status'] === 'paid' ? ($validated['payment_date'] ?? now()->toDateString()) : null,
        ]);

        return back()->with('success', "Payroll #{$payroll->id} status updated to " . strtoupper($validated['status']) . ".");
    }
}
