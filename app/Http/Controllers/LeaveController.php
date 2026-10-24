<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Leave::with(['user', 'reviewer']);

        if ($user->isEmployee()) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->input('leave_type'));
        }

        $leaves = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => $user->isAdmin() ? Leave::count() : Leave::where('user_id', $user->id)->count(),
            'pending' => $user->isAdmin() ? Leave::where('status', 'pending')->count() : Leave::where('user_id', $user->id)->where('status', 'pending')->count(),
            'approved' => $user->isAdmin() ? Leave::where('status', 'approved')->count() : Leave::where('user_id', $user->id)->where('status', 'approved')->count(),
            'rejected' => $user->isAdmin() ? Leave::where('status', 'rejected')->count() : Leave::where('user_id', $user->id)->where('status', 'rejected')->count(),
        ];

        return view('leaves.index', compact('leaves', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_type' => ['required', 'string', 'in:Sick Leave,Vacation Leave,Emergency Leave,Maternity/Paternity Leave,Bereavement Leave,Other'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
        $daysCount = $start->diffInDaysFiltered(fn (Carbon $date) => !$date->isWeekend(), $end) + 1;

        Leave::create([
            'user_id' => Auth::id(),
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'days_count' => max(1, $daysCount),
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Your leave request has been submitted successfully for HR review.');
    }

    public function updateStatus(Request $request, Leave $leave)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'admin_remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $leave->update([
            'status' => $validated['status'],
            'admin_remarks' => $validated['admin_remarks'],
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $statusText = $validated['status'] === 'approved' ? 'approved' : 'rejected';
        return back()->with('success', "Leave request #{$leave->id} has been {$statusText}.");
    }

    public function destroy(Leave $leave)
    {
        $user = Auth::user();

        if ($user->isEmployee() && $leave->user_id !== $user->id) {
            abort(403);
        }

        if ($user->isEmployee() && $leave->status !== 'pending') {
            return back()->with('error', 'Only pending leave requests can be cancelled.');
        }

        $leave->delete();
        return back()->with('success', 'Leave request cancelled.');
    }
}
