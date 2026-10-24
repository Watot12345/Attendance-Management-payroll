<!DOCTYPE html>
<html lang="en" class="bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payslip_#PAY-{{ str_pad($payroll->id, 5, '0', STR_PAD_LEFT) }}_{{ Str::slug($payroll->user->name) }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-container { border: none !important; box-shadow: none !important; margin: 0 !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }
        }
    </style>
</head>
<body class="p-6 font-sans antialiased text-slate-800">
    <!-- Floating Action Toolbar for Screen View -->
    <div class="no-print max-w-3xl mx-auto mb-6 flex justify-between items-center bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <a href="{{ route('payrolls.show', $payroll) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 flex items-center">
            &larr; Back to Payslip Details
        </a>
        <div class="flex items-center space-x-3">
            <button onclick="window.print()" class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md transition flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                Print Payslip
            </button>
        </div>
    </div>

    <!-- Official Printable Payslip Document -->
    <div class="print-container max-w-3xl mx-auto bg-white border border-slate-300 rounded-2xl p-8 sm:p-12 shadow-md space-y-6">
        <!-- Document Header -->
        <div class="flex justify-between items-start pb-6 border-b-2 border-slate-800">
            <div class="flex items-center space-x-4">
                <img src="{{ asset('images/Icon.png') }}" alt="Siglatrolap Logo" class="h-14 w-auto">
                <div>
                    <h1 class="text-2xl font-black uppercase tracking-tight text-slate-900">{{ $company['name'] }}</h1>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $company['address'] }}</p>
                    <p class="text-xs text-slate-500 font-mono">{{ $company['phone'] }} &bull; {{ $company['email'] }}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 bg-slate-900 text-white text-[11px] font-black uppercase tracking-widest rounded-lg">Official Payslip</span>
                <div class="text-xs font-mono font-bold text-slate-700 mt-2">VOUCHER #{{ str_pad($payroll->id, 5, '0', STR_PAD_LEFT) }}</div>
                <div class="text-[10px] text-slate-400">Date Generated: {{ date('F j, Y') }}</div>
            </div>
        </div>

        <!-- Employee & Period Information -->
        <div class="grid grid-cols-2 gap-6 pb-4 border-b border-slate-200 text-xs">
            <div>
                <table class="w-full">
                    <tr>
                        <td class="text-slate-400 py-1 font-semibold uppercase text-[10px]">Employee Name:</td>
                        <td class="font-bold text-slate-900 py-1">{{ $payroll->user->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-400 py-1 font-semibold uppercase text-[10px]">Employee ID:</td>
                        <td class="font-mono text-slate-700 py-1">EMP-{{ str_pad($payroll->user->id, 4, '0', STR_PAD_LEFT) }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-400 py-1 font-semibold uppercase text-[10px]">Position / Role:</td>
                        <td class="text-slate-800 py-1">{{ $payroll->user->position }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-400 py-1 font-semibold uppercase text-[10px]">Department:</td>
                        <td class="text-slate-800 py-1">{{ $payroll->user->department }}</td>
                    </tr>
                </table>
            </div>

            <div>
                <table class="w-full">
                    <tr>
                        <td class="text-slate-400 py-1 font-semibold uppercase text-[10px]">Pay Period:</td>
                        <td class="font-bold text-slate-900 py-1">{{ $payroll->period_start->format('M j, Y') }} - {{ $payroll->period_end->format('M j, Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-400 py-1 font-semibold uppercase text-[10px]">Payment Status:</td>
                        <td class="font-bold text-slate-800 py-1 uppercase">{{ $payroll->status }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-400 py-1 font-semibold uppercase text-[10px]">Daily / Hourly Rate:</td>
                        <td class="font-mono text-slate-700 py-1">&#8369;{{ number_format($payroll->user->daily_rate, 2) }} / &#8369;{{ number_format($payroll->hourly_rate, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-400 py-1 font-semibold uppercase text-[10px]">Days Rendered:</td>
                        <td class="font-mono text-slate-800 py-1">{{ $payroll->days_worked }} days ({{ $payroll->regular_hours }} hrs)</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Earnings and Deductions Table -->
        <div class="grid grid-cols-2 gap-8 text-xs">
            <!-- Earnings Column -->
            <div>
                <h3 class="font-black text-slate-900 uppercase tracking-wider text-[11px] pb-2 border-b-2 border-indigo-600">Earnings</h3>
                <div class="space-y-2 py-3">
                    <div class="flex justify-between">
                        <span class="text-slate-700">Monthly Basic Salary</span>
                        <span class="font-mono font-medium">&#8369;{{ number_format($payroll->basic_salary, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <div>
                            <span class="text-slate-700 block">Overtime Compensation</span>
                            <span class="text-[10px] text-slate-400 font-mono">({{ $payroll->overtime_hours }} hrs)</span>
                        </div>
                        <span class="font-mono font-medium">&#8369;{{ number_format($payroll->overtime_pay, 2) }}</span>
                    </div>
                </div>
                <div class="flex justify-between font-bold text-slate-900 pt-2 border-t border-slate-200">
                    <span>Total Gross Earnings:</span>
                    <span class="font-mono">&#8369;{{ number_format($payroll->gross_pay, 2) }}</span>
                </div>
            </div>

            <!-- Deductions Column -->
            <div>
                <h3 class="font-black text-slate-900 uppercase tracking-wider text-[11px] pb-2 border-b-2 border-rose-600">Deductions</h3>
                <div class="space-y-2 py-3">
                    <div class="flex justify-between">
                        <div>
                            <span class="text-slate-700 block">Tardiness / Late Penalty</span>
                            <span class="text-[10px] text-slate-400 font-mono">({{ $payroll->late_minutes }} mins)</span>
                        </div>
                        <span class="font-mono font-medium">&#8369;{{ number_format($payroll->late_deduction, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-700">SSS Contribution</span>
                        <span class="font-mono font-medium">&#8369;{{ number_format($payroll->sss_deduction, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-700">PhilHealth Insurance</span>
                        <span class="font-mono font-medium">&#8369;{{ number_format($payroll->insurance_deduction, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-700">Withholding Tax</span>
                        <span class="font-mono font-medium">&#8369;{{ number_format($payroll->tax_deduction, 2) }}</span>
                    </div>
                </div>
                <div class="flex justify-between font-bold text-rose-700 pt-2 border-t border-slate-200">
                    <span>Total Deductions:</span>
                    <span class="font-mono">&#8369;{{ number_format($payroll->total_deductions, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Net Pay Grand Total -->
        <div class="p-6 bg-slate-50 border-2 border-slate-800 rounded-xl flex justify-between items-center text-slate-900">
            <div>
                <span class="text-[10px] uppercase font-bold tracking-widest text-slate-500 block">Net Compensation Payable</span>
                <span class="text-xs text-slate-600">Disbursed via {{ $payroll->payment_method }}</span>
            </div>
            <div class="text-right">
                <span class="text-2xl font-black font-mono tracking-tight">&#8369;{{ number_format($payroll->net_pay, 2) }}</span>
            </div>
        </div>

        <!-- Signature Lines -->
        <div class="grid grid-cols-2 gap-12 pt-12 text-xs">
            <div class="text-center">
                <div class="border-b border-slate-400 pb-2"></div>
                <p class="font-bold text-slate-900 mt-2">Authorized Signature</p>
                <p class="text-[10px] text-slate-400">Finance & HR Department</p>
            </div>
            <div class="text-center">
                <div class="border-b border-slate-400 pb-2"></div>
                <p class="font-bold text-slate-900 mt-2">{{ $payroll->user->name }}</p>
                <p class="text-[10px] text-slate-400">Employee Acknowledgment Signature</p>
            </div>
        </div>
    </div>
</body>
</html>
