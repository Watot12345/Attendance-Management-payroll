@extends('layouts.app')

@section('title', 'Company & System Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="pb-3 border-b border-[#00ff00]/40 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-wide uppercase drop-shadow-[0_0_8px_#00ff00]">
                <i class="fa-solid fa-gear text-[#00ff00] mr-2"></i> System Configuration
            </h1>
            <p class="text-xs text-gray-300 font-sans mt-1">Configure company profile, work shift schedules, overtime rates, and statutory payroll deductions.</p>
        </div>
    </div>

    <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Company Profile Section -->
        <div class="sigla-card rounded-2xl p-6 sm:p-8 space-y-4">
            <h2 class="text-sm font-bold text-[#00ff00] uppercase tracking-wider pb-3 border-b border-gray-700/60 flex items-center font-sans">
                <i class="fa-solid fa-building mr-2 text-[rgb(0,187,255)]"></i>
                Company Information
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 font-sans text-xs">
                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Company Trade Name</label>
                    <input type="text" name="company_name" required value="{{ old('company_name', $settings['company_name'] ?? 'Siglatrolap Attendance & Payroll') }}"
                           class="w-full px-3 py-2 bg-black/70 border-2 border-gray-700 rounded-xl text-white text-xs focus:border-[#00ff00] focus:outline-none transition">
                </div>
                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Official HR Email</label>
                    <input type="email" name="company_email" required value="{{ old('company_email', $settings['company_email'] ?? 'hr@siglatrolap.com') }}"
                           class="w-full px-3 py-2 bg-black/70 border-2 border-gray-700 rounded-xl text-white text-xs focus:border-[#00ff00] focus:outline-none transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 font-sans text-xs">
                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Contact Phone</label>
                    <input type="text" name="company_phone" value="{{ old('company_phone', $settings['company_phone'] ?? '+63 912 345 6789') }}"
                           class="w-full px-3 py-2 bg-black/70 border-2 border-gray-700 rounded-xl text-white text-xs focus:border-[#00ff00] focus:outline-none transition">
                </div>
                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Office Address</label>
                    <input type="text" name="company_address" value="{{ old('company_address', $settings['company_address'] ?? 'Metro Manila, Philippines') }}"
                           class="w-full px-3 py-2 bg-black/70 border-2 border-gray-700 rounded-xl text-white text-xs focus:border-[#00ff00] focus:outline-none transition">
                </div>
            </div>
        </div>

        <!-- Attendance & Work Hours Section -->
        <div class="sigla-card rounded-2xl p-6 sm:p-8 space-y-4">
            <h2 class="text-sm font-bold text-[#00ff00] uppercase tracking-wider pb-3 border-b border-gray-700/60 flex items-center font-sans">
                <i class="fa-solid fa-business-time mr-2 text-[rgb(0,187,255)]"></i>
                Work Shifts & Grace Periods
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 font-sans text-xs">
                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Shift Start Time (Check-In)</label>
                    <input type="time" name="work_start_time" required value="{{ old('work_start_time', substr($settings['work_start_time'] ?? '08:00', 0, 5)) }}"
                           class="w-full px-3 py-2 bg-black/70 border-2 border-gray-700 rounded-xl text-white text-xs focus:border-[#00ff00] focus:outline-none transition">
                    <p class="text-[10px] text-gray-400 mt-1">Arrivals past this time are tagged as Late.</p>
                </div>
                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Shift End Time (Check-Out)</label>
                    <input type="time" name="work_end_time" required value="{{ old('work_end_time', substr($settings['work_end_time'] ?? '17:00', 0, 5)) }}"
                           class="w-full px-3 py-2 bg-black/70 border-2 border-gray-700 rounded-xl text-white text-xs focus:border-[#00ff00] focus:outline-none transition">
                    <p class="text-[10px] text-gray-400 mt-1">Regular shift ending time.</p>
                </div>
                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Regular Hours Per Day</label>
                    <input type="number" step="0.5" name="regular_hours_per_day" required value="{{ old('regular_hours_per_day', $settings['regular_hours_per_day'] ?? '8') }}"
                           class="w-full px-3 py-2 bg-black/70 border-2 border-gray-700 rounded-xl text-white text-xs focus:border-[#00ff00] focus:outline-none transition">
                </div>
            </div>
        </div>

        <!-- Payroll & Deductions Formulas -->
        <div class="sigla-card rounded-2xl p-6 sm:p-8 space-y-4">
            <h2 class="text-sm font-bold text-[#00ff00] uppercase tracking-wider pb-3 border-b border-gray-700/60 flex items-center font-sans">
                <i class="fa-solid fa-calculator mr-2 text-[rgb(0,187,255)]"></i>
                Payroll Rates & Statutory Deductions
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 font-sans text-xs">
                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Overtime Rate (30m block)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-[#00ff00] font-bold text-xs">&#8369;</span>
                        <input type="number" step="0.01" name="overtime_rate_30m" required value="{{ old('overtime_rate_30m', $settings['overtime_rate_30m'] ?? '70') }}"
                               class="w-full pl-7 pr-3 py-2 bg-black/70 border-2 border-gray-700 rounded-xl text-white text-xs focus:border-[#00ff00] focus:outline-none transition font-mono">
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1">Paid per 30 minutes extra.</p>
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Late Penalty (30m block)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-rose-400 font-bold text-xs">&#8369;</span>
                        <input type="number" step="0.01" name="late_penalty_30m" required value="{{ old('late_penalty_30m', $settings['late_penalty_30m'] ?? '300') }}"
                               class="w-full pl-7 pr-3 py-2 bg-black/70 border-2 border-gray-700 rounded-xl text-white text-xs focus:border-[#00ff00] focus:outline-none transition font-mono">
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1">Deducted per 30m tardiness.</p>
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Monthly SSS (PHP)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-xs">&#8369;</span>
                        <input type="number" step="0.01" name="sss_monthly_deduction" required value="{{ old('sss_monthly_deduction', $settings['sss_monthly_deduction'] ?? '300') }}"
                               class="w-full pl-7 pr-3 py-2 bg-black/70 border-2 border-gray-700 rounded-xl text-white text-xs focus:border-[#00ff00] focus:outline-none transition font-mono">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Monthly Insurance (PHP)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-xs">&#8369;</span>
                        <input type="number" step="0.01" name="insurance_monthly_deduction" required value="{{ old('insurance_monthly_deduction', $settings['insurance_monthly_deduction'] ?? '300') }}"
                               class="w-full pl-7 pr-3 py-2 bg-black/70 border-2 border-gray-700 rounded-xl text-white text-xs focus:border-[#00ff00] focus:outline-none transition font-mono">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit" class="btn-sigla-green px-8 py-3 rounded-xl font-bold text-xs uppercase tracking-wider flex items-center shadow-[0_0_15px_rgba(0,255,0,0.3)]">
                <i class="fa-solid fa-floppy-disk mr-2"></i> Save System Settings
            </button>
        </div>
    </form>
</div>
@endsection
