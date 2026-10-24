@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Iconic Welcome Tagline matching legacy home.php -->
    <div class="text-center py-4 space-y-2">
        <p class="text-xl sm:text-2xl font-bold tracking-wider">
            HI <span class="one-name">{{ strtoupper(Auth::user()->username ?? Auth::user()->name) }}</span>!, WELCOME TO SIGLATROLAP
        </p>
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-widest uppercase">
            THE SPARKS THAT<span class="solo-one">&nbsp;TRANSFORMS</span> IDEAS INTO <span class="solo-two">REALITY</span>
        </h1>
        <div class="flex justify-center items-center space-x-3 pt-2">
            <a href="{{ route('scanner') }}" target="_blank" class="employee-button">
                <i class="fas fa-qrcode mr-1"></i> QR KIOSK TERMINAL
            </a>
            <button onclick="document.getElementById('manualAttendanceModal').showModal()" class="employee-button">
                <i class="fas fa-plus mr-1"></i> MANUAL LOG ENTRY
            </button>
            <a href="{{ route('employees.create') }}" class="employee-button">
                <i class="fas fa-user-plus mr-1"></i> NEW EMPLOYEE
            </a>
        </div>
    </div>

    <!-- Live Executive Metrics in Dark Glass Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="sigla-card p-5">
            <span class="text-xs uppercase font-mono tracking-wider text-slate-400">Total Staff</span>
            <div class="text-3xl font-extrabold font-mono mt-1 text-white">{{ $totalEmployees }}</div>
            <p class="text-xs text-green-400 mt-1 font-mono">Active Personnel</p>
        </div>

        <div class="sigla-card p-5">
            <span class="text-xs uppercase font-mono tracking-wider text-slate-400">Present Today</span>
            <div class="text-3xl font-extrabold font-mono mt-1" style="color: rgb(0, 255, 0);">
                {{ $presentCount }} <span class="text-xs text-slate-400">/ {{ $totalEmployees }}</span>
            </div>
            <p class="text-xs text-slate-300 mt-1 font-mono">{{ $onTimeCount }} On Time &bull; {{ $lateCount }} Late</p>
        </div>

        <div class="sigla-card p-5">
            <span class="text-xs uppercase font-mono tracking-wider text-slate-400">Pending Leaves</span>
            <div class="text-3xl font-extrabold font-mono mt-1" style="color: rgb(0, 187, 255);">
                {{ $pendingLeavesCount }}
            </div>
            <p class="text-xs text-cyan-300 mt-1 font-mono">Awaiting Review</p>
        </div>

        <div class="sigla-card p-5">
            <span class="text-xs uppercase font-mono tracking-wider text-slate-400">Monthly Payroll</span>
            <div class="text-2xl font-extrabold font-mono mt-1 text-white">
                &#8369;{{ number_format($totalMonthlyPayroll, 2) }}
            </div>
            <p class="text-xs text-green-400 mt-1 font-mono">Disbursed Compensation</p>
        </div>
    </div>

    <!-- Main Content Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Today's Attendance Table with Green Borders -->
        <div class="lg:col-span-7 sigla-card p-6 overflow-hidden">
            <div class="flex justify-between items-center pb-4 mb-4 border-b border-green-800">
                <div>
                    <h2 class="text-lg font-bold text-white tracking-wide" style="color: #00ff00;">
                        <i class="fas fa-clipboard-check mr-2"></i> Today's Attendance Records
                    </h2>
                    <p class="text-xs text-slate-400 font-mono">{{ \Carbon\Carbon::today()->format('l, F j, Y') }}</p>
                </div>
                <a href="{{ route('attendances.index', ['date_from' => date('Y-m-d')]) }}" class="employee-button text-xs !py-1 !px-3">
                    View Full &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="sigla-table text-xs">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Status</th>
                            <th>Hours</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($todayAttendances as $att)
                            <tr>
                                <td class="flex items-center space-x-2.5">
                                    <img src="{{ $att->user->avatar_url }}" alt="{{ $att->user->name }}" class="w-7 h-7 rounded-full object-cover border border-green-500">
                                    <div>
                                        <div class="font-bold text-white">{{ $att->user->name }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">{{ $att->user->department }}</div>
                                    </div>
                                </td>
                                <td class="font-mono text-green-400 font-bold">
                                    {{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('h:i:s A') : '—' }}
                                </td>
                                <td class="font-mono text-slate-300">
                                    {{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('h:i:s A') : 'Active Shift' }}
                                </td>
                                <td>
                                    @if($att->status === 'On Time')
                                        <span class="text-xs font-bold font-mono" style="color: rgb(0, 255, 0);">On Time</span>
                                    @elseif($att->status === 'Late')
                                        <span class="text-xs font-bold font-mono text-rose-400">Late ({{ $att->late_minutes }}m)</span>
                                    @elseif($att->status === 'Overtime')
                                        <span class="text-xs font-bold font-mono text-purple-400">Overtime</span>
                                    @else
                                        <span class="text-xs font-mono text-slate-300">{{ $att->status }}</span>
                                    @endif
                                </td>
                                <td class="font-mono text-white font-bold">
                                    {{ $att->work_hours > 0 ? $att->work_hours . ' hrs' : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-6 text-slate-400 font-mono">
                                    No employee attendance recorded yet today.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pending Leave Approvals -->
        <div class="lg:col-span-5 sigla-card p-6 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center pb-4 mb-4 border-b border-green-800">
                    <div>
                        <h2 class="text-lg font-bold text-white tracking-wide" style="color: rgb(0, 187, 255);">
                            <i class="fas fa-envelope-open mr-2"></i> Pending Leave Requests
                        </h2>
                        <p class="text-xs text-slate-400 font-mono">Requires Authorization</p>
                    </div>
                    <a href="{{ route('leaves.index') }}" class="employee-button text-xs !py-1 !px-3">
                        All ({{ $pendingLeavesCount }})
                    </a>
                </div>

                <div class="divide-y divide-green-950/60 space-y-3">
                    @forelse($pendingLeaves as $leave)
                        <div class="pt-3 space-y-2">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center space-x-2">
                                    <img src="{{ $leave->user->avatar_url }}" alt="{{ $leave->user->name }}" class="w-6 h-6 rounded-full object-cover border border-green-500">
                                    <span class="font-bold text-white text-xs">{{ $leave->user->name }}</span>
                                </div>
                                <span class="text-[11px] font-mono text-green-400">{{ $leave->leave_type }} &bull; {{ $leave->days_count }} day(s)</span>
                            </div>
                            <p class="text-xs text-slate-300 italic p-2 rounded bg-black/60 border border-green-900/50">
                                "{{ Str::limit($leave->reason, 90) }}"
                            </p>
                            <div class="flex justify-end space-x-2">
                                <form action="{{ route('leaves.update-status', $leave) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="px-3 py-1 text-xs font-bold rounded border border-red-500 text-red-400 hover:bg-red-600 hover:text-white transition">
                                        Reject
                                    </button>
                                </form>
                                <form action="{{ route('leaves.update-status', $leave) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="px-3 py-1 text-xs font-bold rounded border border-green-500 bg-green-500 text-black hover:bg-green-400 transition">
                                        Approve
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-center py-8 text-slate-400 text-xs font-mono">
                            No pending leave applications. All clear!
                        </p>
                    @endforelse
                </div>
            </div>

            <div class="pt-4 border-t border-green-900/40 text-right">
                <a href="{{ route('payrolls.index') }}" class="text-xs text-green-400 font-bold hover:underline">
                    Access Payroll Management &rarr;
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Manual Attendance Modal (closedby="any") -->
<dialog id="manualAttendanceModal" closedby="any" class="rounded-2xl p-0 w-full max-w-lg shadow-2xl backdrop:bg-black/80 backdrop:backdrop-blur-sm border border-green-500 bg-black text-white">
    <div class="p-6">
        <div class="flex justify-between items-center pb-4 border-b border-green-800">
            <h3 class="text-base font-bold text-green-400">Record Manual Attendance Entry</h3>
            <button onclick="document.getElementById('manualAttendanceModal').close()" class="text-slate-400 hover:text-white text-base font-bold">&times;</button>
        </div>

        <form action="{{ route('attendances.manual') }}" method="POST" class="space-y-4 pt-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Select Employee</label>
                <select name="user_id" required class="sigla-input w-full text-xs">
                    @foreach(\App\Models\User::where('role', 'employee')->orderBy('name')->get() as $emp)
                        <option value="{{ $emp->id }}" class="bg-black text-white">{{ $emp->name }} ({{ $emp->department }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Date</label>
                    <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="sigla-input w-full text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Status</label>
                    <select name="status" class="sigla-input w-full text-xs">
                        <option value="On Time" class="bg-black text-white">On Time</option>
                        <option value="Late" class="bg-black text-white">Late</option>
                        <option value="Overtime" class="bg-black text-white">Overtime</option>
                        <option value="Under Time" class="bg-black text-white">Under Time</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Check In Time</label>
                    <input type="time" name="check_in" required value="08:00" class="sigla-input w-full text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Check Out Time</label>
                    <input type="time" name="check_out" value="17:00" class="sigla-input w-full text-xs">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Reason / Notes</label>
                <input type="text" name="notes" placeholder="e.g. Field assignment punch adjustment" class="sigla-input w-full text-xs">
            </div>

            <div class="flex justify-end space-x-2 pt-3 border-t border-green-800">
                <button type="button" onclick="document.getElementById('manualAttendanceModal').close()" class="px-4 py-2 text-xs text-slate-400 hover:text-white">Cancel</button>
                <button type="submit" class="btn-sigla-green text-xs !py-2 !px-4">Save Attendance</button>
            </div>
        </form>
    </div>
</dialog>
@endsection
