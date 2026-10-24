@extends('layouts.app')

@section('title', 'Payslip #' . $payroll->id)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-green-800">
        <div>
            <div class="flex items-center space-x-2 text-xs font-mono">
                <a href="{{ route('payrolls.index') }}" class="text-green-400 hover:underline font-bold">&larr; Back to Payroll List</a>
                <span class="text-slate-500">&bull;</span>
                <span class="text-slate-300">VOUCHER #{{ str_pad($payroll->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
            <h1 class="text-2xl font-black text-white tracking-wider uppercase mt-1">Official Employee Payslip</h1>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('payrolls.print', $payroll) }}" target="_blank" class="employee-button">
                <i class="fas fa-print mr-1"></i> Print / Export PDF
            </a>
            @if(Auth::user()->isAdmin())
                <form action="{{ route('payrolls.update-status', $payroll) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    @if($payroll->status !== 'paid')
                        <input type="hidden" name="status" value="paid">
                        <button type="submit" class="btn-sigla-green text-xs !py-1.5 !px-3">
                            <i class="fas fa-check mr-1"></i> Mark as Paid
                        </button>
                    @else
                        <input type="hidden" name="status" value="processed">
                        <button type="submit" class="employee-button text-xs !py-1.5 !px-3">
                            Set as Processed
                        </button>
                    @endif
                </form>
            @endif
        </div>
    </div>

    <!-- Main Payslip Container matching legacy .payslip-container -->
    <div class="sigla-card p-6 sm:p-10 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-6 border-b border-green-800 gap-4">
            <div class="flex items-center space-x-4">
                <img src="{{ asset('images/Iconwhite.png') }}" alt="Siglatrolap" class="h-12 w-auto">
                <div>
                    <h2 class="text-xl font-bold uppercase tracking-wider" style="color: rgb(0, 255, 0);">
                        Siglatrolap Innovations Inc
                    </h2>
                    <p class="text-xs text-slate-400 font-mono">Employee Compensation Voucher</p>
                </div>
            </div>

            <div class="text-right font-mono text-xs">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Pay Period:</span>
                <div class="text-sm font-bold text-white">{{ $payroll->period_start->format('F j, Y') }} &ndash; {{ $payroll->period_end->format('F j, Y') }}</div>
                <div class="mt-1">
                    <span class="employee-button !py-0.5 !px-2.5 text-[10px]">
                        STATUS: {{ strtoupper($payroll->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Employee Details matching legacy .employee-details -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 rounded-xl bg-black/60 border border-green-900/60 text-xs font-mono">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase">Employee:</span>
                <span class="font-bold text-white text-sm font-sans">{{ $payroll->user->name }}</span>
                <div class="text-[10px] text-green-400 font-sans">{{ $payroll->user->position }}</div>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase">Department:</span>
                <span class="font-bold text-white">{{ $payroll->user->department }}</span>
                <div class="text-[10px] text-slate-400">ID #{{ str_pad($payroll->user->id, 4, '0', STR_PAD_LEFT) }}</div>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase">Hourly / Daily:</span>
                <span class="text-green-400">&#8369;{{ number_format($payroll->hourly_rate, 2) }}/hr</span>
                <div class="text-[10px] text-slate-400">&#8369;{{ number_format($payroll->user->daily_rate, 2) }}/day</div>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase">Days Worked:</span>
                <span class="font-bold text-white text-sm">{{ $payroll->days_worked }} days</span>
                <div class="text-[10px] text-slate-400">{{ $payroll->regular_hours }} reg hrs</div>
            </div>
        </div>

        <!-- Salary Breakdown Table matching legacy .salary-breakdown -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
            <!-- Earnings Table -->
            <div class="overflow-hidden">
                <table class="sigla-table text-xs">
                    <thead>
                        <tr>
                            <th>EARNINGS ITEM</th>
                            <th class="text-right">AMOUNT (PHP)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Basic Monthly Salary</td>
                            <td class="text-right font-mono font-bold text-white">&#8369;{{ number_format($payroll->basic_salary, 2) }}</td>
                        </tr>
                        <tr>
                            <td>
                                Overtime Pay
                                <span class="block text-[10px] text-purple-400 font-mono">({{ $payroll->overtime_hours }} extra hrs)</span>
                            </td>
                            <td class="text-right font-mono font-bold text-purple-400">+&#8369;{{ number_format($payroll->overtime_pay, 2) }}</td>
                        </tr>
                        <tr class="bg-black/90 font-bold">
                            <td style="color: rgb(0, 255, 0);">TOTAL GROSS EARNINGS</td>
                            <td class="text-right font-mono" style="color: rgb(0, 255, 0);">&#8369;{{ number_format($payroll->gross_pay, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Deductions Table -->
            <div class="overflow-hidden">
                <table class="sigla-table text-xs">
                    <thead>
                        <tr>
                            <th style="color: #ff5555; background: rgba(50, 0, 0, 0.4);">DEDUCTION ITEM</th>
                            <th class="text-right" style="color: #ff5555; background: rgba(50, 0, 0, 0.4);">AMOUNT (PHP)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                Tardiness / Late Penalty
                                <span class="block text-[10px] text-red-400 font-mono">({{ $payroll->late_minutes }} minutes)</span>
                            </td>
                            <td class="text-right font-mono text-red-400">-&#8369;{{ number_format($payroll->late_deduction, 2) }}</td>
                        </tr>
                        <tr>
                            <td>SSS Contribution</td>
                            <td class="text-right font-mono text-red-400">-&#8369;{{ number_format($payroll->sss_deduction, 2) }}</td>
                        </tr>
                        <tr>
                            <td>PhilHealth Insurance</td>
                            <td class="text-right font-mono text-red-400">-&#8369;{{ number_format($payroll->insurance_deduction, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Withholding Income Tax</td>
                            <td class="text-right font-mono text-red-400">-&#8369;{{ number_format($payroll->tax_deduction, 2) }}</td>
                        </tr>
                        <tr class="bg-black/90 font-bold">
                            <td class="text-red-400">TOTAL DEDUCTIONS</td>
                            <td class="text-right font-mono text-red-400">-&#8369;{{ number_format($payroll->total_deductions, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Net Take Home Highlight Box -->
        <div class="p-6 rounded-2xl bg-black/80 border-2 border-green-500 flex flex-col sm:flex-row justify-between items-center gap-4 text-center sm:text-left">
            <div>
                <span class="text-xs uppercase font-mono tracking-widest text-slate-400 block">Net Compensation Payable</span>
                <span class="text-xs text-green-400 font-mono">Payment Method: {{ $payroll->payment_method }}</span>
            </div>
            <div class="text-right font-mono">
                <div class="text-3xl sm:text-4xl font-black" style="color: rgb(0, 255, 0);">
                    &#8369;{{ number_format($payroll->net_pay, 2) }}
                </div>
            </div>
        </div>

        <!-- Attendance Logs Audit for this Period -->
        <div class="pt-4 border-t border-green-800">
            <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-3" style="color: #00ff00;">
                <i class="fas fa-list-check mr-2"></i> Attendance Audit Logs for Period
            </h3>
            <div class="overflow-x-auto max-h-60 rounded-xl">
                <table class="sigla-table text-xs">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status</th>
                            <th>Late/OT</th>
                            <th>Hours</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $att)
                            <tr>
                                <td class="font-bold text-white">{{ $att->date->format('M j, Y (D)') }}</td>
                                <td class="font-mono text-green-400">{{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('h:i A') : '—' }}</td>
                                <td class="font-mono text-slate-300">{{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('h:i A') : '—' }}</td>
                                <td>{{ $att->status }}</td>
                                <td class="font-mono text-[11px]">
                                    @if($att->late_minutes > 0)
                                        <span class="text-red-400">-{{ $att->late_minutes }}m</span>
                                    @elseif($att->overtime_minutes > 0)
                                        <span class="text-purple-400">+{{ $att->overtime_minutes }}m</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="font-mono text-white font-bold">{{ $att->work_hours }} hrs</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-slate-400 font-mono">
                                    No daily attendance records mapped for this period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
