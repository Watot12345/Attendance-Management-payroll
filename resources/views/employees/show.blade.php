@extends('layouts.app')

@section('title', 'Employee Profile - ' . $employee->name)

@section('content')
<div class="space-y-8">
    <div class="flex items-center space-x-2 text-xs font-mono">
        <a href="{{ route('employees.index') }}" class="text-green-400 hover:underline font-bold">&larr; Back to Employees</a>
        <span class="text-slate-500">&bull;</span>
        <span class="text-slate-300">ID: EMP-{{ str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}</span>
    </div>

    <!-- Header Card -->
    <div class="sigla-card p-6 sm:p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div class="flex items-center space-x-5">
            <img src="{{ $employee->avatar_url }}" alt="{{ $employee->name }}" class="w-20 h-20 rounded-full object-cover border-2 border-green-500 shadow-lg">
            <div>
                <div class="flex items-center space-x-3">
                    <h1 class="text-2xl font-black text-white tracking-wide uppercase">{{ $employee->name }}</h1>
                    <span class="font-mono text-xs px-2 py-0.5 rounded {{ $employee->status === 'active' ? 'bg-green-500/20 text-green-300 border border-green-500' : 'bg-red-500/20 text-red-300 border border-red-500' }}">
                        {{ strtoupper($employee->status) }}
                    </span>
                </div>
                <p class="text-xs text-green-400 font-mono mt-0.5">{{ $employee->position }} &bull; {{ $employee->department }}</p>
                <p class="text-xs text-slate-300 font-mono mt-1">Email: {{ $employee->email }} &bull; Phone: {{ $employee->phone ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('employees.edit', $employee) }}" class="employee-button">
                <i class="fas fa-edit mr-1"></i> Edit Details
            </a>
            <form action="{{ route('employees.regenerate-qr', $employee) }}" method="POST" onsubmit="return confirm('Regenerate QR pass?');" class="inline">
                @csrf
                <button type="submit" class="employee-button">
                    <i class="fas fa-sync-alt mr-1"></i> Regenerate QR
                </button>
            </form>
        </div>
    </div>

    <!-- Main Grid: Digital ID Card + Rates Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Digital QR Badge Card matching legacy profile_card -->
        <div class="lg:col-span-5 sigla-card p-6 sm:p-8 flex flex-col items-center justify-between text-center space-y-4">
            <div class="w-full flex justify-between items-center pb-3 border-b border-green-800">
                <div class="flex items-center space-x-2">
                    <img src="{{ asset('images/Iconwhite.png') }}" alt="Logo" class="h-6 w-auto">
                    <span class="text-xs uppercase font-bold tracking-widest text-green-400">Siglatrolap Pass</span>
                </div>
                <span class="text-[10px] font-mono text-slate-400">#{{ str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}</span>
            </div>

            <div class="p-3 bg-white rounded-2xl shadow-inner inline-block my-2">
                <canvas id="employeeQrCanvas" class="rounded-xl mx-auto"></canvas>
            </div>

            <div>
                <h3 class="text-lg font-bold text-white uppercase">{{ $employee->name }}</h3>
                <p class="text-xs text-green-400 font-mono">{{ $employee->position }}</p>
                <p class="text-[10px] font-mono text-slate-400 mt-1 break-all">SECRET: {{ $employee->qr_secret }}</p>
            </div>

            <p class="text-[11px] text-slate-400 pt-2 border-t border-green-900/60">
                Scan this pass on the office QR Kiosk terminal to clock in and out.
            </p>
        </div>

        <!-- Compensation and Rates Summary -->
        <div class="lg:col-span-7 space-y-6">
            <div class="sigla-card p-6 space-y-4">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider pb-2 border-b border-green-800" style="color: #00ff00;">
                    <i class="fas fa-coins mr-1"></i> Compensation Structure
                </h3>
                <div class="grid grid-cols-3 gap-4 text-center font-mono">
                    <div class="p-3 rounded-xl bg-black/60 border border-green-950">
                        <span class="text-slate-400 block text-[10px] uppercase font-sans">Monthly Basic</span>
                        <div class="text-lg font-extrabold text-white mt-0.5">&#8369;{{ number_format($employee->basic_salary, 2) }}</div>
                    </div>
                    <div class="p-3 rounded-xl bg-black/60 border border-green-950">
                        <span class="text-slate-400 block text-[10px] uppercase font-sans">Daily Rate</span>
                        <div class="text-lg font-extrabold text-green-400 mt-0.5">&#8369;{{ number_format($employee->daily_rate, 2) }}</div>
                    </div>
                    <div class="p-3 rounded-xl bg-black/60 border border-green-950">
                        <span class="text-slate-400 block text-[10px] uppercase font-sans">Hourly Rate</span>
                        <div class="text-lg font-extrabold text-cyan-400 mt-0.5">&#8369;{{ number_format($employee->hourly_rate, 2) }}</div>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-black/40 border border-green-900/40 text-xs font-mono flex justify-between">
                    <div>Hire Date: <span class="text-white">{{ $employee->hire_date ? $employee->hire_date->format('F j, Y') : 'N/A' }}</span></div>
                    <div>Username: <span class="text-green-400 font-bold">{{ $employee->username }}</span></div>
                </div>
            </div>

            <!-- Recent Attendance Records -->
            <div class="sigla-card p-6 overflow-hidden">
                <div class="flex justify-between items-center pb-3 mb-3 border-b border-green-800">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-green-400">
                        <i class="fas fa-history mr-1"></i> Recent Attendance Records
                    </h3>
                    <a href="{{ route('attendances.index', ['user_id' => $employee->id]) }}" class="employee-button text-xs !py-0.5 !px-2.5">
                        View All
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="sigla-table text-xs">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>In</th>
                                <th>Out</th>
                                <th>Status</th>
                                <th>Hours</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAttendances as $att)
                                <tr>
                                    <td class="font-bold text-white">{{ $att->date->format('M j, Y') }}</td>
                                    <td class="font-mono text-green-400">{{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('h:i A') : '—' }}</td>
                                    <td class="font-mono text-slate-300">{{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('h:i A') : 'Active' }}</td>
                                    <td>{{ $att->status }}</td>
                                    <td class="font-mono text-white font-bold">{{ $att->work_hours }} hrs</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-slate-400 font-mono">No attendance records.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.QRCode) {
            const qrCanvas = document.getElementById('employeeQrCanvas');
            const secret = "{{ $employee->qr_secret }}";
            window.QRCode.toCanvas(qrCanvas, secret, { width: 200, margin: 2, color: { dark: '#000000', light: '#ffffff' } }, function (err) {
                if (err) console.error(err);
            });
        }
    });
</script>
@endpush
