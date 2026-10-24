@extends('layouts.app')

@section('title', 'Leave Management')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-green-800">
        <div>
            <h1 class="text-2xl font-black text-white tracking-wider uppercase">
                <i class="fas fa-calendar-alt text-green-400 mr-2"></i> Leave Applications & Authorizations
            </h1>
            <p class="text-xs text-slate-400 font-mono mt-1">Vacation, sick, emergency, and bereavement leave workflows.</p>
        </div>
        <div>
            <button onclick="document.getElementById('requestLeaveModal').showModal()" class="btn-sigla-green text-xs !py-2 !px-4">
                <i class="fas fa-plus mr-1"></i> Apply for Leave
            </button>
        </div>
    </div>

    <!-- Status Tabs in Theme -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
        <a href="{{ route('leaves.index') }}" class="sigla-card p-4 transition {{ !request('status') ? 'border-green-400 ring-2 ring-green-500/30' : '' }}">
            <span class="text-xs font-mono font-bold text-slate-400 uppercase tracking-wider">All Applications</span>
            <div class="text-2xl font-black font-mono text-white mt-1">{{ $stats['total'] }}</div>
        </a>
        <a href="{{ route('leaves.index', ['status' => 'pending']) }}" class="sigla-card p-4 transition {{ request('status') === 'pending' ? 'border-amber-400 ring-2 ring-amber-500/30' : '' }}">
            <span class="text-xs font-mono font-bold text-amber-400 uppercase tracking-wider">Pending Review</span>
            <div class="text-2xl font-black font-mono text-amber-400 mt-1">{{ $stats['pending'] }}</div>
        </a>
        <a href="{{ route('leaves.index', ['status' => 'approved']) }}" class="sigla-card p-4 transition {{ request('status') === 'approved' ? 'border-green-400 ring-2 ring-green-500/30' : '' }}">
            <span class="text-xs font-mono font-bold text-green-400 uppercase tracking-wider">Approved</span>
            <div class="text-2xl font-black font-mono text-green-400 mt-1">{{ $stats['approved'] }}</div>
        </a>
        <a href="{{ route('leaves.index', ['status' => 'rejected']) }}" class="sigla-card p-4 transition {{ request('status') === 'rejected' ? 'border-red-400 ring-2 ring-red-500/30' : '' }}">
            <span class="text-xs font-mono font-bold text-red-400 uppercase tracking-wider">Rejected</span>
            <div class="text-2xl font-black font-mono text-red-400 mt-1">{{ $stats['rejected'] }}</div>
        </a>
    </div>

    <!-- Leaves Table with Green Borders -->
    <div class="sigla-card p-6 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="sigla-table text-xs">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Leave Type</th>
                        <th>Period & Duration</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Reviewed By</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaves as $leave)
                        <tr>
                            <td class="flex items-center space-x-2.5">
                                <img src="{{ $leave->user->avatar_url }}" alt="{{ $leave->user->name }}" class="w-7 h-7 rounded-full object-cover border border-green-500">
                                <div>
                                    <div class="font-bold text-white">{{ $leave->user->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $leave->user->department }}</div>
                                </div>
                            </td>
                            <td class="font-bold text-white">
                                {{ $leave->leave_type }}
                            </td>
                            <td class="font-mono text-slate-300">
                                <div>{{ $leave->start_date->format('M j, Y') }} - {{ $leave->end_date->format('M j, Y') }}</div>
                                <span class="text-green-400 font-bold text-[10px]">{{ $leave->days_count }} business day(s)</span>
                            </td>
                            <td class="max-w-xs text-slate-300 italic">
                                "{{ Str::limit($leave->reason, 100) }}"
                                @if($leave->admin_remarks)
                                    <div class="mt-1 text-[10px] text-green-400 font-mono bg-black/60 p-1.5 rounded border border-green-900/60">
                                        <span class="font-bold text-white">HR:</span> {{ $leave->admin_remarks }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($leave->status === 'approved')
                                    <span class="font-mono font-bold text-xs text-green-400">APPROVED</span>
                                @elseif($leave->status === 'rejected')
                                    <span class="font-mono font-bold text-xs text-red-400">REJECTED</span>
                                @else
                                    <span class="font-mono font-bold text-xs text-amber-400">PENDING</span>
                                @endif
                            </td>
                            <td class="font-mono text-slate-400 text-xs">
                                @if($leave->reviewer)
                                    <span class="text-white">{{ $leave->reviewer->name }}</span>
                                    <div class="text-[10px] text-slate-500">{{ $leave->reviewed_at ? $leave->reviewed_at->format('M j, Y') : '' }}</div>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap">
                                @if(Auth::user()->isAdmin() && $leave->status === 'pending')
                                    <div class="inline-flex space-x-1">
                                        <form action="{{ route('leaves.update-status', $leave) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="px-2.5 py-1 text-[11px] font-bold text-black bg-green-500 hover:bg-green-400 rounded transition">
                                                Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('leaves.update-status', $leave) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="px-2.5 py-1 text-[11px] font-bold text-red-400 border border-red-500 hover:bg-red-600 hover:text-white rounded transition">
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                @elseif(Auth::user()->id === $leave->user_id && $leave->status === 'pending')
                                    <form action="{{ route('leaves.destroy', $leave) }}" method="POST" onsubmit="return confirm('Cancel this leave request?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-bold text-red-400 hover:underline">
                                            Cancel
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-slate-400 font-mono">
                                No leave requests recorded for this selection.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-green-900/40">
            {{ $leaves->links() }}
        </div>
    </div>
</div>

<!-- Request Leave Modal -->
<dialog id="requestLeaveModal" closedby="any" class="rounded-2xl p-0 w-full max-w-lg shadow-2xl backdrop:bg-black/80 backdrop:backdrop-blur-sm border border-green-500 bg-black text-white">
    <div class="p-6">
        <div class="flex justify-between items-center pb-4 border-b border-green-800">
            <h3 class="text-base font-bold text-green-400">Submit Leave Application</h3>
            <button onclick="document.getElementById('requestLeaveModal').close()" class="text-slate-400 hover:text-white text-base font-bold">&times;</button>
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
