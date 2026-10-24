<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'employee');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->input('department'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $employees = $query->orderBy('name')->paginate(12)->withQueryString();
        $departments = User::whereNotNull('department')->distinct()->pluck('department');

        return view('employees.index', compact('employees', 'departments'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'phone' => ['nullable', 'string', 'max:20'],
            'department' => ['required', 'string', 'max:100'],
            'position' => ['required', 'string', 'max:100'],
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'hire_date' => ['nullable', 'date'],
        ]);

        $basicSalary = (float) $validated['basic_salary'];
        $dailyRate = round($basicSalary / 22, 2);
        $hourlyRate = round($dailyRate / 8, 2);

        $employee = User::create([
            'name' => $validated['name'],
            'username' => strtolower($validated['username']),
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'role' => 'employee',
            'qr_secret' => Str::random(32),
            'phone' => $validated['phone'],
            'department' => $validated['department'],
            'position' => $validated['position'],
            'basic_salary' => $basicSalary,
            'daily_rate' => $dailyRate,
            'hourly_rate' => $hourlyRate,
            'status' => 'active',
            'hire_date' => $validated['hire_date'] ?? now()->toDateString(),
        ]);

        return redirect()->route('employees.show', $employee)->with('success', 'Employee ' . $employee->name . ' registered successfully!');
    }

    public function show(User $employee)
    {
        $recentAttendances = Attendance::where('user_id', $employee->id)
            ->latest('date')
            ->take(10)
            ->get();

        $recentLeaves = Leave::where('user_id', $employee->id)
            ->latest()
            ->take(5)
            ->get();

        $recentPayrolls = Payroll::where('user_id', $employee->id)
            ->latest('period_end')
            ->take(5)
            ->get();

        return view('employees.show', compact('employee', 'recentAttendances', 'recentLeaves', 'recentPayrolls'));
    }

    public function edit(User $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, User $employee)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($employee->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($employee->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'department' => ['required', 'string', 'max:100'],
            'position' => ['required', 'string', 'max:100'],
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
            'hire_date' => ['nullable', 'date'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $basicSalary = (float) $validated['basic_salary'];
        $dailyRate = round($basicSalary / 22, 2);
        $hourlyRate = round($dailyRate / 8, 2);

        $updateData = [
            'name' => $validated['name'],
            'username' => strtolower($validated['username']),
            'email' => strtolower($validated['email']),
            'phone' => $validated['phone'],
            'department' => $validated['department'],
            'position' => $validated['position'],
            'basic_salary' => $basicSalary,
            'daily_rate' => $dailyRate,
            'hourly_rate' => $hourlyRate,
            'status' => $validated['status'],
            'hire_date' => $validated['hire_date'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $employee->update($updateData);

        return redirect()->route('employees.show', $employee)->with('success', 'Employee details updated successfully.');
    }

    public function regenerateQr(User $employee)
    {
        $employee->update([
            'qr_secret' => Str::random(32),
        ]);

        return back()->with('success', 'New QR Code generated for ' . $employee->name . '.');
    }

    public function destroy(User $employee)
    {
        $employee->update(['status' => 'inactive']);
        return redirect()->route('employees.index')->with('success', 'Employee status marked as inactive.');
    }
}
