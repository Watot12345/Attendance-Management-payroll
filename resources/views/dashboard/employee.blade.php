@extends('layouts.app')

@section('title', 'Staff Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Tagline Banner matching legacy home.php -->
    <div class="text-center py-4 space-y-2">
        <p class="text-xl sm:text-2xl font-bold tracking-wider">
            HI <span class="one-name">{{ strtoupper(Auth::user()->username ?? Auth::user()->name) }}</span>!, WELCOME TO SIGLATROLAP
        </p>
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-widest uppercase">
            THE SPARKS THAT<span class="solo-one">&nbsp;TRANSFORMS</span> IDEAS INTO <span class="solo-two">REALITY</span>
        </h1>
        <p class="text-xs text-slate-300 font-mono">{{ Auth::user()->position }} &bull; {{ Auth::user()->department }} Department</p>
    </div>

    <!-- Clocking Hero & Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Live Clock & Attendance Controls matching legacy attendance.php -->
        <div class="lg:col-span-6 sigla-card p-6 sm:p-8 flex flex-col justify-between text-center space-y-4">
            <div>
                <span class="text-xs font-mono font-bold tracking-widest text-slate-400 uppercase">Current Official Time</span>
                <div id="liveClock" class="live-clock text-4xl sm:text-5xl font-black font-mono my-2" style="color: rgb(0, 255, 0);">
                    00:00:00
                </div>
                <div class="text-xs text-slate-300 font-mono">{{ \Carbon\Carbon::today()->format('l, F j, Y') }}</div>
            </div>

            <!-- Current Shift Status -->
            <div class="p-3 rounded-xl bg-black/60 border border-green-900/50 flex justify-between items-center text-xs font-mono">
                <div>
                    <span class="text-slate-400 block text-[10px]">TIME IN:</span>
                    <span class="text-green-400 font-bold text-sm">
                        {{ $todayAttendance && $todayAttendance->check_in ? \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i:s A') : '—' }}
                    </span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px]">TIME OUT:</span>
                    <span class="text-white font-bold text-sm">
                        {{ $todayAttendance && $todayAttendance->check_out ? \Carbon\Carbon::parse($todayAttendance->check_out)->format('h:i:s A') : 'Active' }}
                    </span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px]">STATUS:</span>
                    <span class="font-bold text-sm" style="color: {{ $todayAttendance ? 'rgb(0, 255, 0)' : '#ff3333' }};">
                        {{ $todayAttendance ? $todayAttendance->status : 'Not Clocked In' }}
                    </span>
                </div>
            </div>

            <!-- Clocking Buttons matching legacy attendance-btn -->
            <div class="grid grid-cols-2 gap-4 pt-2">
                <form action="{{ route('attendances.clock') }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="in">
                    <button type="submit" {{ $todayAttendance ? 'disabled' : '' }}
                            class="btn-sigla-green w-full py-3.5 text-sm uppercase tracking-wider disabled:opacity-30 disabled:cursor-not-allowed">
                        <i class="fas fa-sign-in-alt mr-2"></i> TIME IN
                    </button>
                </form>

                <form action="{{ route('attendances.clock') }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="out">
                    <button type="submit" {{ !$todayAttendance || !empty($todayAttendance->check_out) ? 'disabled' : '' }}
                            class="btn-sigla-red w-full py-3.5 text-sm uppercase tracking-wider disabled:opacity-30 disabled:cursor-not-allowed">
                        <i class="fas fa-sign-out-alt mr-2"></i> TIME OUT
                    </button>
                </form>
            </div>

            <!-- Digital Pass Trigger -->
            <div class="pt-2">
                <button onclick="document.getElementById('myQrModal').showModal()" class="employee-button w-full text-center">
                    <i class="fas fa-qrcode mr-2"></i> VIEW MY DIGITAL QR PASS
                </button>
            </div>
        </div>

        <!-- Monthly Counters & Payslip Snapshot -->
        <div class="lg:col-span-6 flex flex-col justify-between space-y-4">
            <div class="grid grid-cols-3 gap-4 text-center">
                <div class="sigla-card p-4">
                    <span class="text-[11px] font-mono text-slate-400 uppercase">Days Worked</span>
                    <div class="text-2xl font-black font-mono mt-1 text-white">{{ $currentMonthDays }}</div>
                    <span class="text-[10px] text-green-400 font-mono">This Month</span>
                </div>
                <div class="sigla-card p-4">
                    <span class="text-[11px] font-mono text-slate-400 uppercase">Tardy Minutes</span>
                    <div class="text-2xl font-black font-mono mt-1 text-red-400">{{ $currentMonthLate }}m</div>
                    <span class="text-[10px] text-slate-400 font-mono">Deduction basis</span>
                </div>
                <div class="sigla-card p-4">
                    <span class="text-[11px] font-mono text-slate-400 uppercase">Overtime</span>
                    <div class="text-2xl font-black font-mono mt-1 text-purple-400">{{ round($currentMonthOt / 60, 1) }}h</div>
                    <span class="text-[10px] text-purple-400 font-mono">Extra hours</span>
                </div>
            </div>

            <!-- Latest Official Payslip Card -->
            <div class="sigla-card p-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div>
                    <span class="text-xs font-mono font-bold uppercase tracking-wider" style="color: #00ff00;">
                        <i class="fas fa-file-invoice-dollar mr-1"></i> Latest Official Payslip
                    </span>
                    @if($latestPayroll)
                        <div class="text-3xl font-extrabold font-mono mt-1 text-white">
                            &#8369;{{ number_format($latestPayroll->net_pay, 2) }}
                        </div>
                        <p class="text-xs text-slate-400 font-mono">Period: {{ $latestPayroll->period_start->format('M j') }} - {{ $latestPayroll->period_end->format('M j, Y') }}</p>
                    @else
                        <div class="text-lg font-bold text-slate-400 mt-1">Pending Generation</div>
                    @endif
                </div>

                @if($latestPayroll)
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('payrolls.show', $latestPayroll) }}" class="employee-button">
                            Details
                        </a>
                        <a href="{{ route('payrolls.print', $latestPayroll) }}" target="_blank" class="employee-button" title="Print Payslip">
                            <i class="fas fa-print"></i>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Quick Apply Leave Link -->
            <div class="sigla-card p-4 flex justify-between items-center">
                <span class="text-xs text-slate-300">Need time off? Submit your leave request.</span>
                <button onclick="document.getElementById('requestLeaveModal').showModal()" class="employee-button text-xs">
                    <i class="fas fa-plus mr-1"></i> Apply Leave
                </button>
            </div>
        </div>
    </div>

    <!-- Attendance History & Leaves Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Recent Attendance Log with Green Table -->
        <div class="lg:col-span-7 sigla-card p-6 overflow-hidden">
            <div class="flex justify-between items-center pb-3 mb-4 border-b border-green-800">
                <h2 class="text-base font-bold text-white tracking-wide" style="color: #00ff00;">
                    <i class="fas fa-history mr-2"></i> Recent Attendance Records
                </h2>
                <a href="{{ route('attendances.index') }}" class="employee-button text-xs !py-1 !px-3">
                    View All &rarr;
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
                                <td class="font-bold text-white">{{ $att->date->format('M j, Y (D)') }}</td>
                                <td class="font-mono text-green-400">{{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('h:i A') : '—' }}</td>
                                <td class="font-mono text-slate-300">{{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('h:i A') : 'Active' }}</td>
                                <td>
                                    @if($att->status === 'On Time')
                                        <span class="font-bold font-mono text-green-400">On Time</span>
                                    @elseif($att->status === 'Late')
                                        <span class="font-bold font-mono text-red-400">Late ({{ $att->late_minutes }}m)</span>
                                    @elseif($att->status === 'Overtime')
                                        <span class="font-bold font-mono text-purple-400">Overtime</span>
                                    @else
                                        <span class="font-mono text-slate-300">{{ $att->status }}</span>
                                    @endif
                                </td>
                                <td class="font-mono text-white font-bold">{{ $att->work_hours > 0 ? $att->work_hours . ' hrs' : '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-6 text-slate-400 font-mono">
                                    No attendance records found yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Leave Applications -->
        <div class="lg:col-span-5 sigla-card p-6 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center pb-3 mb-4 border-b border-green-800">
                    <h2 class="text-base font-bold text-white tracking-wide" style="color: rgb(0, 187, 255);">
                        <i class="fas fa-calendar-alt mr-2"></i> My Leave Applications
                    </h2>
                    <button onclick="document.getElementById('requestLeaveModal').showModal()" class="employee-button text-xs !py-1 !px-3">
                        + New
                    </button>
                </div>

                <div class="divide-y divide-green-950/60 space-y-3">
                    @forelse($leaves as $leave)
                        <div class="pt-3 space-y-1.5 text-xs">
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-white">{{ $leave->leave_type }}</span>
                                @if($leave->status === 'approved')
                                    <span class="text-xs font-bold font-mono text-green-400">Approved</span>
                                @elseif($leave->status === 'rejected')
                                    <span class="text-xs font-bold font-mono text-red-400">Rejected</span>
                                @else
                                    <span class="text-xs font-bold font-mono text-amber-400">Pending Review</span>
                                @endif
                            </div>
                            <div class="text-[11px] font-mono text-slate-400">
                                {{ $leave->start_date->format('M j') }} - {{ $leave->end_date->format('M j, Y') }} ({{ $leave->days_count }} days)
                            </div>
                            <p class="text-slate-300 italic p-2 rounded bg-black/60 border border-green-900/40">
                                "{{ $leave->reason }}"
                            </p>
                        </div>
                    @empty
                        <p class="text-center py-8 text-slate-400 text-xs font-mono">
                            No leave applications submitted yet.
                        </p>
                    @endforelse
                </div>
            </div>

            <div class="pt-4 border-t border-green-900/40 text-right">
                <a href="{{ route('leaves.index') }}" class="text-xs text-green-400 font-bold hover:underline">
                    View Leave Archive &rarr;
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Employee QR Pass Modal -->
<dialog id="myQrModal" closedby="any" class="rounded-3xl p-0 w-full max-w-sm shadow-2xl backdrop:bg-black/80 backdrop:backdrop-blur-sm border border-green-500 bg-black text-white">
    <div class="p-6 text-center space-y-4">
        <div class="flex justify-between items-center pb-2 border-b border-green-800">
            <h3 class="text-sm font-bold uppercase tracking-wider text-green-400">Digital Attendance Pass</h3>
            <button onclick="document.getElementById('myQrModal').close()" class="text-slate-400 hover:text-white text-lg font-bold">&times;</button>
        </div>

        <div class="p-4 bg-white rounded-2xl inline-block shadow-inner">
            <canvas id="qrCodeCanvas" class="mx-auto rounded-xl"></canvas>
        </div>

        <div>
            <h4 class="font-bold text-white text-lg">{{ Auth::user()->name }}</h4>
            <p class="text-xs text-green-400 font-mono">{{ Auth::user()->position }} &bull; {{ Auth::user()->department }}</p>
            <p class="text-[10px] font-mono text-slate-400 mt-1">TOKEN: {{ Auth::user()->qr_secret }}</p>
        </div>

        <p class="text-xs text-slate-400">
            Hold this code in front of the kiosk terminal camera to punch your time-in and time-out.
        </p>
    </div>
</dialog>

<!-- Request Leave Modal -->
<dialog id="requestLeaveModal" closedby="any" class="rounded-2xl p-0 w-full max-w-lg shadow-2xl backdrop:bg-black/80 backdrop:backdrop-blur-sm border border-green-500 bg-black text-white">
    <div class="p-6">
        <div class="flex justify-between items-center pb-4 border-b border-green-800">
            <h3 class="text-base font-bold text-green-400">Submit Leave Application</h3>
            <button onclick="document.getElementById('requestLeaveModal').close()" class="text-slate-400 hover:text-white text-sm font-bold">&times;</button>
        </div>

        <form action="{{ route('leaves.store') }}" method="POST" class="space-y-4 pt-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Leave Category</label>
                <select name="leave_type" required class="sigla-input w-full text-xs">
                    <option value="Vacation Leave" class="bg-black text-white">Vacation Leave</option>
                    <option value="Sick Leave" class="bg-black text-white">Sick Leave</option>
                    <option value="Emergency Leave" class="bg-black text-white">Emergency Leave</option>
                    <option value="Bereavement Leave" class="bg-black text-white">Bereavement Leave</option>
                    <option value="Maternity/Paternity Leave" class="bg-black text-white">Maternity / Paternity Leave</option>
                    <option value="Other" class="bg-black text-white">Other</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Start Date</label>
                    <input type="date" name="start_date" required min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" class="sigla-input w-full text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">End Date</label>
                    <input type="date" name="end_date" required min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" class="sigla-input w-full text-xs">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Reason for Leave</label>
                <textarea name="reason" rows="3" required placeholder="Describe reason for leave..." class="sigla-input w-full text-xs"></textarea>
            </div>

            <div class="flex justify-end space-x-2 pt-3 border-t border-green-800">
                <button type="button" onclick="document.getElementById('requestLeaveModal').close()" class="px-4 py-2 text-xs text-slate-400 hover:text-white">Cancel</button>
                <button type="submit" class="btn-sigla-green text-xs !py-2 !px-4">Submit Application</button>
            </div>
        </form>
    </div>
</dialog>
@endsection

@push('scripts')
<script>
    // Live Digital Clock
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', { hour12: false });
        const clockEl = document.getElementById('liveClock');
        if (clockEl) {
            clockEl.textContent = timeString;
        }
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Render QR Code onto Canvas
    document.addEventListener('DOMContentLoaded', () => {
        if (window.QRCode) {
            const qrCanvas = document.getElementById('qrCodeCanvas');
            const secret = "{{ Auth::user()->qr_secret }}";
            window.QRCode.toCanvas(qrCanvas, secret, { width: 200, margin: 2, color: { dark: '#000000', light: '#ffffff' } }, function (err) {
                if (err) console.error(err);
            });
        }
    });
</script>
@endpush
