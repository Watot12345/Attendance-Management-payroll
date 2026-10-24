@extends('layouts.app')

@section('title', 'Attendance Records')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-green-800">
        <div>
            <h1 class="text-2xl font-black text-white tracking-wider uppercase">
                <i class="fas fa-clipboard-list text-green-400 mr-2"></i> Employee Attendance Records
            </h1>
            <p class="text-xs text-slate-400 font-mono mt-1">Audit log of daily time-in, time-out, tardiness, and overtime.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('scanner') }}" target="_blank" class="employee-button">
                <i class="fas fa-qrcode mr-1"></i> QR KIOSK
            </a>
            @if(Auth::user()->isAdmin())
                <button onclick="document.getElementById('manualAttendanceModal').showModal()" class="btn-sigla-green text-xs !py-1.5 !px-3">
                    <i class="fas fa-plus mr-1"></i> Manual Log
                </button>
            @endif
        </div>
    </div>

    @if(Auth::user()->isAdmin() && $employees->count() > 0)
        <!-- Employee Quick Filter Buttons matching legacy records.php -->
        <div class="sigla-card p-4">
            <div class="text-xs font-mono font-bold text-green-400 mb-2 uppercase tracking-wider">
                <i class="fas fa-users mr-1"></i> Quick Filter by Employee:
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('attendances.index') }}" class="employee-button {{ !request('user_id') ? 'active' : '' }}">
                    All Employees
                </a>
                @foreach($employees as $emp)
                    <a href="{{ route('attendances.index', array_merge(request()->except('user_id'), ['user_id' => $emp->id])) }}"
                       class="employee-button {{ request('user_id') == $emp->id ? 'active' : '' }}">
                        {{ $emp->name }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Filter Form -->
    <div class="sigla-card p-4">
        <form method="GET" action="{{ route('attendances.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
            @if(Auth::user()->isAdmin())
                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Select Employee</label>
                    <select name="user_id" class="sigla-input w-full text-xs">
                        <option value="" class="bg-black text-white">All Employees</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('user_id') == $emp->id ? 'selected' : '' }} class="bg-black text-white">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <label class="block font-semibold text-slate-300 mb-1">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="sigla-input w-full text-xs">
            </div>

            <div>
                <label class="block font-semibold text-slate-300 mb-1">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="sigla-input w-full text-xs">
            </div>

            <div>
                <label class="block font-semibold text-slate-300 mb-1">Status</label>
                <select name="status" class="sigla-input w-full text-xs">
                    <option value="" class="bg-black text-white">All Statuses</option>
                    <option value="On Time" {{ request('status') === 'On Time' ? 'selected' : '' }} class="bg-black text-white">On Time</option>
                    <option value="Late" {{ request('status') === 'Late' ? 'selected' : '' }} class="bg-black text-white">Late</option>
                    <option value="Overtime" {{ request('status') === 'Overtime' ? 'selected' : '' }} class="bg-black text-white">Overtime</option>
                    <option value="Under Time" {{ request('status') === 'Under Time' ? 'selected' : '' }} class="bg-black text-white">Under Time</option>
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="btn-sigla-green flex-1 text-xs !py-2">
                    Filter
                </button>
                <a href="{{ route('attendances.index') }}" class="btn-sigla-primary text-xs !py-2 text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Attendance Table with Green Borders matching legacy styled.css -->
    <div class="sigla-card p-6 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="sigla-table text-xs">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Date</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Status</th>
                        <th>Late / OT</th>
                        <th>Work Hours</th>
                        <th>Method</th>
                        @if(Auth::user()->isAdmin())
                            <th class="text-right">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $att)
                        <tr>
                            <td class="flex items-center space-x-2.5">
                                <img src="{{ $att->user->avatar_url }}" alt="{{ $att->user->name }}" class="w-7 h-7 rounded-full object-cover border border-green-500">
                                <div>
                                    <div class="font-bold text-white">{{ $att->user->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $att->user->department }}</div>
                                </div>
                            </td>
                            <td class="font-mono text-slate-200">
                                {{ $att->date->format('M j, Y (D)') }}
                            </td>
                            <td class="font-mono text-green-400 font-bold">
                                {{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('h:i:s A') : '—' }}
                            </td>
                            <td class="font-mono text-slate-300">
                                {{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('h:i:s A') : '<span class="text-amber-400 font-sans font-semibold">Active</span>' }}
                            </td>
                            <td>
                                @if($att->status === 'On Time')
                                    <span class="font-bold font-mono text-green-400">On Time</span>
                                @elseif($att->status === 'Late')
                                    <span class="font-bold font-mono text-red-400">Late</span>
                                @elseif($att->status === 'Overtime')
                                    <span class="font-bold font-mono text-purple-400">Overtime</span>
                                @else
                                    <span class="font-mono text-slate-300">{{ $att->status }}</span>
                                @endif
                            </td>
                            <td class="font-mono text-[11px]">
                                @if($att->late_minutes > 0)
                                    <span class="text-red-400 font-bold">-{{ $att->late_minutes }}m</span>
                                @elseif($att->overtime_minutes > 0)
                                    <span class="text-purple-400 font-bold">+{{ round($att->overtime_minutes / 60, 1) }}h</span>
                                @else
                                    <span class="text-slate-500">—</span>
                                @endif
                            </td>
                            <td class="font-mono text-white font-bold">
                                {{ $att->work_hours > 0 ? $att->work_hours . ' hrs' : '—' }}
                            </td>
                            <td>
                                <span class="font-mono text-[10px] px-2 py-0.5 rounded border border-green-900 bg-black text-green-400 uppercase">
                                    {{ $att->scan_method }}
                                </span>
                            </td>
                            @if(Auth::user()->isAdmin())
                                <td class="text-right">
                                    <form action="{{ route('attendances.destroy', $att) }}" method="POST" onsubmit="return confirm('Delete this attendance record?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-slate-400 hover:text-red-400 transition" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ Auth::user()->isAdmin() ? 9 : 8 }}" class="text-center py-10 text-slate-400 font-mono">
                                No attendance records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-green-900/40">
            {{ $attendances->links() }}
        </div>
    </div>
</div>

@if(Auth::user()->isAdmin())
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
                    @foreach($employees as $emp)
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
                <button type="submit" class="btn-sigla-green text-xs !py-2 !px-4">Save Entry</button>
            </div>
        </form>
    </div>
</dialog>
@endif
@endsection
