<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Payroll;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today()->format('Y-m-d');

        if ($user->isAdmin()) {
            // Admin Metrics
            $totalEmployees = User::where('role', 'employee')->count();
            $todayAttendances = Attendance::with('user')->whereDate('date', $today)->get();
            $presentCount = $todayAttendances->count();
            $onTimeCount = $todayAttendances->where('status', 'On Time')->count();
            $lateCount = $todayAttendances->where('status', 'Late')->count();
            $overtimeCount = $todayAttendances->where('status', 'Overtime')->count();

            $pendingLeaves = Leave::with('user')->where('status', 'pending')->latest()->take(5)->get();
            $pendingLeavesCount = Leave::where('status', 'pending')->count();

            $recentAttendances = Attendance::with('user')->latest()->take(10)->get();

            $totalMonthlyPayroll = Payroll::whereMonth('period_end', Carbon::today()->month)
                ->whereYear('period_end', Carbon::today()->year)
                ->sum('net_pay');

            return view('dashboard.admin', compact(
                'totalEmployees',
                'presentCount',
                'onTimeCount',
                'lateCount',
                'overtimeCount',
                'pendingLeaves',
                'pendingLeavesCount',
                'recentAttendances',
                'totalMonthlyPayroll',
                'todayAttendances'
            ));
        }

        // Employee Metrics
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $recentAttendances = Attendance::where('user_id', $user->id)
            ->latest('date')
            ->take(7)
            ->get();

        $leaves = Leave::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $latestPayroll = Payroll::where('user_id', $user->id)
            ->latest('period_end')
            ->first();

        $currentMonthDays = Attendance::where('user_id', $user->id)
            ->whereMonth('date', Carbon::today()->month)
            ->whereYear('date', Carbon::today()->year)
            ->count();

        $currentMonthLate = Attendance::where('user_id', $user->id)
            ->whereMonth('date', Carbon::today()->month)
            ->whereYear('date', Carbon::today()->year)
            ->sum('late_minutes');

        $currentMonthOt = Attendance::where('user_id', $user->id)
            ->whereMonth('date', Carbon::today()->month)
            ->whereYear('date', Carbon::today()->year)
            ->sum('overtime_minutes');

        return view('dashboard.employee', compact(
            'todayAttendance',
            'recentAttendances',
            'leaves',
            'latestPayroll',
            'currentMonthDays',
            'currentMonthLate',
            'currentMonthOt'
        ));
    }
}
