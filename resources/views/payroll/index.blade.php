@extends('layouts.app')

@section('title', 'Payroll & Payslips')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-green-800">
        <div>
            <h1 class="text-2xl font-black text-white tracking-wider uppercase">
                <i class="fas fa-wallet text-green-400 mr-2"></i> Employee Payroll & Payslip Records
            </h1>
            <p class="text-xs text-slate-400 font-mono mt-1">Automated salary calculations, overtime pay, and official vouchers.</p>
        </div>
        <div>
            @if(Auth::user()->isAdmin())
                <button onclick="document.getElementById('generatePayrollModal').showModal()" class="btn-sigla-green text-xs !py-2 !px-4">
                    <i class="fas fa-calculator mr-1"></i> Calculate & Generate Payroll
                </button>
            @endif
        </div>
    </div>

    <!-- Summary Box -->
    <div class="sigla-card p-6 flex flex-col sm:flex-row justify-between items-center gap-4">
        <div>
            <span class="text-xs font-mono font-bold tracking-widest text-slate-400 uppercase">Total Net Compensation Disbursed</span>
            <div class="text-3xl sm:text-4xl font-black font-mono mt-1" style="color: rgb(0, 255, 0);">
                &#8369;{{ number_format($totalDisbursed, 2) }}
            </div>
            <p class="text-xs text-slate-400 font-mono mt-0.5">Processed official vouchers</p>
        </div>
        <div class="text-right">
            <span class="employee-button">
                OT Rate: &#8369;{{ \App\Models\CompanySetting::get('overtime_rate_30m', 70) }} / 30m
            </span>
        </div>
    </div>

    <!-- Period Filter Form matching legacy .period-selector -->
    <div class="sigla-card p-4">
        <form method="GET" action="{{ route('payrolls.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
            @if(Auth::user()->isAdmin())
                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Employee</label>
                    <select name="user_id" class="sigla-input w-full text-xs">
                        <option value="" class="bg-black text-white">All Employees</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('user_id') == $emp->id ? 'selected' : '' }} class="bg-black text-white">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <label class="block font-semibold text-slate-300 mb-1">Select Pay Period</label>
                <input type="month" name="period" value="{{ request('period') }}" class="sigla-input w-full text-xs">
            </div>

            <div>
                <label class="block font-semibold text-slate-300 mb-1">Status</label>
                <select name="status" class="sigla-input w-full text-xs">
                    <option value="" class="bg-black text-white">All Statuses</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }} class="bg-black text-white">Draft</option>
                    <option value="processed" {{ request('status') === 'processed' ? 'selected' : '' }} class="bg-black text-white">Processed</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }} class="bg-black text-white">Paid</option>
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="btn-sigla-green flex-1 text-xs !py-2">
                    Generate Payslip
                </button>
                <a href="{{ route('payrolls.index') }}" class="btn-sigla-primary text-xs !py-2 text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Payroll Table with Green Borders matching legacy payroll.php -->
    <div class="sigla-card p-6 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="sigla-table text-xs">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Pay Period</th>
                        <th>Basic Salary</th>
                        <th>Overtime Pay</th>
                        <th>Gross Pay</th>
                        <th>Deductions</th>
                        <th>Net Take-Home</th>
                        <th>Status</th>
                        <th class="text-right">Voucher</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrolls as $pay)
                        <tr>
                            <td class="flex items-center space-x-2.5">
                                <img src="{{ $pay->user->avatar_url }}" alt="{{ $pay->user->name }}" class="w-7 h-7 rounded-full object-cover border border-green-500">
                                <div>
                                    <div class="font-bold text-white">{{ $pay->user->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $pay->user->department }}</div>
                                </div>
                            </td>
                            <td class="font-mono text-slate-300">
                                {{ $pay->period_start->format('M j') }} - {{ $pay->period_end->format('M j, Y') }}
                            </td>
                            <td class="font-mono text-white">
                                &#8369;{{ number_format($pay->basic_salary, 2) }}
                            </td>
                            <td class="font-mono text-purple-400 font-bold">
                                +&#8369;{{ number_format($pay->overtime_pay, 2) }}
                            </td>
                            <td class="font-mono text-slate-200">
                                &#8369;{{ number_format($pay->gross_pay, 2) }}
                            </td>
                            <td class="font-mono text-red-400 font-bold">
                                -&#8369;{{ number_format($pay->total_deductions, 2) }}
                            </td>
                            <td class="font-mono font-black text-sm" style="color: rgb(0, 255, 0);">
                                &#8369;{{ number_format($pay->net_pay, 2) }}
                            </td>
                            <td>
                                @if($pay->status === 'paid')
                                    <span class="font-mono font-bold text-xs" style="color: rgb(0, 255, 0);">PAID</span>
                                @elseif($pay->status === 'processed')
                                    <span class="font-mono font-bold text-xs" style="color: rgb(0, 187, 255);">PROCESSED</span>
                                @else
                                    <span class="font-mono text-xs text-slate-400">DRAFT</span>
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <a href="{{ route('payrolls.show', $pay) }}" class="employee-button !py-1 !px-2.5 text-xs mr-1">
                                    View
                                </a>
                                <a href="{{ route('payrolls.print', $pay) }}" target="_blank" class="employee-button !py-1 !px-2.5 text-xs" title="Print Payslip">
                                    <i class="fas fa-print"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-10 text-slate-400 font-mono">
                                No payroll records found for this period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-green-900/40">
            {{ $payrolls->links() }}
        </div>
    </div>
</div>

@if(Auth::user()->isAdmin())
<!-- Generate Payroll Modal -->
<dialog id="generatePayrollModal" closedby="any" class="rounded-2xl p-0 w-full max-w-lg shadow-2xl backdrop:bg-black/80 backdrop:backdrop-blur-sm border border-green-500 bg-black text-white">
    <div class="p-6">
        <div class="flex justify-between items-center pb-4 border-b border-green-800">
            <h3 class="text-base font-bold text-green-400">Run Automated Payroll Calculation</h3>
            <button onclick="document.getElementById('generatePayrollModal').close()" class="text-slate-400 hover:text-white text-base font-bold">&times;</button>
        </div>

        <form action="{{ route('payrolls.generate') }}" method="POST" class="space-y-4 pt-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Target Employees</label>
                <select name="user_id" class="sigla-input w-full text-xs">
                    <option value="" class="bg-black text-white">All Active Employees (Company-Wide)</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" class="bg-black text-white">{{ $emp->name }} ({{ $emp->position }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Period Start Date</label>
                    <input type="date" name="period_start" required value="{{ date('Y-m-01') }}" class="sigla-input w-full text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Period End Date</label>
                    <input type="date" name="period_end" required value="{{ date('Y-m-t') }}" class="sigla-input w-full text-xs">
                </div>
            </div>

            <div class="p-3 rounded-xl bg-black/60 border border-green-900/60 text-xs text-slate-300 font-mono space-y-1">
                <span class="text-green-400 font-bold block">Calculation Parameters:</span>
                <div>&bull; Overtime Rate: &#8369;{{ \App\Models\CompanySetting::get('overtime_rate_30m', 70) }} per 30m block</div>
                <div>&bull; Late Tardy Penalty: &#8369;{{ \App\Models\CompanySetting::get('late_penalty_30m', 300) }} per 30m block</div>
                <div>&bull; Statutory Deductions: SSS, PhilHealth, Tax brackets</div>
            </div>

            <div class="flex justify-end space-x-2 pt-3 border-t border-green-800">
                <button type="button" onclick="document.getElementById('generatePayrollModal').close()" class="px-4 py-2 text-xs text-slate-400 hover:text-white">Cancel</button>
                <button type="submit" class="btn-sigla-green text-xs !py-2 !px-4">Run Payroll Engine</button>
            </div>
        </form>
    </div>
</dialog>
@endif
@endsection
