<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\CompanySetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Attendance::with('user');

        if ($user->isEmployee()) {
            $query->where('user_id', $user->id);
        } elseif ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->input('date_to'));
        }

        $attendances = $query->latest('date')->latest('check_in')->paginate(15)->withQueryString();
        $employees = $user->isAdmin() ? User::where('role', 'employee')->orderBy('name')->get() : collect();

        return view('attendance.index', compact('attendances', 'employees'));
    }

    public function scanner()
    {
        $today = Carbon::today()->format('Y-m-d');
        $recentScans = Attendance::with('user')
            ->whereDate('date', $today)
            ->latest('updated_at')
            ->take(10)
            ->get();

        return view('attendance.scanner', compact('recentScans'));
    }

    public function verifyQr(Request $request)
    {
        $request->validate([
            'qr_secret' => ['required', 'string'],
            'action' => ['nullable', 'string', 'in:auto,in,out'],
        ]);

        $qrSecret = trim($request->input('qr_secret'));
        $user = User::where('qr_secret', $qrSecret)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unrecognized QR Code. No matching employee found.',
            ], 404);
        }

        $now = Carbon::now();
        $today = $now->format('Y-m-d');
        $currentTime = $now->format('H:i:s');
        $workStartTime = CompanySetting::get('work_start_time', '08:00:00');
        $workEndTime = CompanySetting::get('work_end_time', '17:00:00');

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $requestedAction = $request->input('action', 'auto');

        // Logic for Clock In
        if (!$attendance || ($requestedAction === 'in' && !$attendance)) {
            $scheduledStart = Carbon::createFromTimeString($workStartTime);
            $checkInTimeObj = Carbon::createFromTimeString($currentTime);

            $lateMinutes = 0;
            $status = 'On Time';

            if ($checkInTimeObj->gt($scheduledStart)) {
                $lateMinutes = $checkInTimeObj->diffInMinutes($scheduledStart);
                $status = 'Late';
            }

            $attendance = Attendance::create([
                'user_id' => $user->id,
                'date' => $today,
                'check_in' => $currentTime,
                'status' => $status,
                'late_minutes' => $lateMinutes,
                'scan_method' => 'qr',
                'notes' => 'Clocked in via QR Scanner',
            ]);

            return response()->json([
                'success' => true,
                'type' => 'in',
                'user' => [
                    'name' => $user->name,
                    'department' => $user->department,
                    'position' => $user->position,
                    'avatar' => $user->avatar_url,
                ],
                'time' => Carbon::parse($currentTime)->format('h:i A'),
                'status' => $status,
                'message' => 'Time-In Recorded Successfully for ' . $user->name . '!',
            ]);
        }

        // Logic for Clock Out
        if ($attendance && empty($attendance->check_out)) {
            if ($requestedAction === 'in') {
                return response()->json([
                    'success' => false,
                    'message' => $user->name . ' has already clocked in for today at ' . Carbon::parse($attendance->check_in)->format('h:i A') . '.',
                ], 422);
            }

            $checkInObj = Carbon::parse($attendance->date->format('Y-m-d') . ' ' . $attendance->check_in);
            $checkOutObj = Carbon::parse($today . ' ' . $currentTime);
            $regularEndObj = Carbon::parse($today . ' ' . $workEndTime);

            $workHours = round($checkOutObj->diffInMinutes($checkInObj) / 60, 2);
            $overtimeMinutes = 0;
            $status = $attendance->status;

            if ($checkOutObj->gt($regularEndObj)) {
                $overtimeMinutes = $checkOutObj->diffInMinutes($regularEndObj);
                if ($overtimeMinutes >= 30) {
                    $status = 'Overtime';
                }
            }

            $attendance->update([
                'check_out' => $currentTime,
                'work_hours' => $workHours,
                'overtime_minutes' => $overtimeMinutes,
                'status' => $status,
            ]);

            return response()->json([
                'success' => true,
                'type' => 'out',
                'user' => [
                    'name' => $user->name,
                    'department' => $user->department,
                    'position' => $user->position,
                    'avatar' => $user->avatar_url,
                ],
                'time' => Carbon::parse($currentTime)->format('h:i A'),
                'hours_rendered' => $workHours . ' hrs',
                'status' => $status,
                'message' => 'Time-Out Recorded Successfully for ' . $user->name . '!',
            ]);
        }

        // Already fully clocked out today
        return response()->json([
            'success' => false,
            'message' => $user->name . ' has already completed attendance today (In: ' . Carbon::parse($attendance->check_in)->format('h:i A') . ', Out: ' . Carbon::parse($attendance->check_out)->format('h:i A') . ').',
        ], 422);
    }

    public function selfClock(Request $request)
    {
        $user = Auth::user();
        $now = Carbon::now();
        $today = $now->format('Y-m-d');
        $currentTime = $now->format('H:i:s');
        $workStartTime = CompanySetting::get('work_start_time', '08:00:00');
        $workEndTime = CompanySetting::get('work_end_time', '17:00:00');

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $action = $request->input('action'); // 'in' or 'out'

        if ($action === 'in') {
            if ($attendance) {
                return back()->with('error', 'You have already checked in today at ' . Carbon::parse($attendance->check_in)->format('h:i A'));
            }

            $scheduledStart = Carbon::createFromTimeString($workStartTime);
            $checkInTimeObj = Carbon::createFromTimeString($currentTime);
            $lateMinutes = 0;
            $status = 'On Time';

            if ($checkInTimeObj->gt($scheduledStart)) {
                $lateMinutes = $checkInTimeObj->diffInMinutes($scheduledStart);
                $status = 'Late';
            }

            Attendance::create([
                'user_id' => $user->id,
                'date' => $today,
                'check_in' => $currentTime,
                'status' => $status,
                'late_minutes' => $lateMinutes,
                'scan_method' => 'web',
                'notes' => 'Clocked in via portal button',
            ]);

            return back()->with('success', 'Clocked in successfully at ' . Carbon::parse($currentTime)->format('h:i A') . ' (' . $status . ')');
        } elseif ($action === 'out') {
            if (!$attendance) {
                return back()->with('error', 'You must clock in before clocking out.');
            }

            if (!empty($attendance->check_out)) {
                return back()->with('error', 'You have already clocked out today.');
            }

            $checkInObj = Carbon::parse($attendance->date->format('Y-m-d') . ' ' . $attendance->check_in);
            $checkOutObj = Carbon::parse($today . ' ' . $currentTime);
            $regularEndObj = Carbon::parse($today . ' ' . $workEndTime);

            $workHours = round($checkOutObj->diffInMinutes($checkInObj) / 60, 2);
            $overtimeMinutes = 0;
            $status = $attendance->status;

            if ($checkOutObj->gt($regularEndObj)) {
                $overtimeMinutes = $checkOutObj->diffInMinutes($regularEndObj);
                if ($overtimeMinutes >= 30) {
                    $status = 'Overtime';
                }
            }

            $attendance->update([
                'check_out' => $currentTime,
                'work_hours' => $workHours,
                'overtime_minutes' => $overtimeMinutes,
                'status' => $status,
            ]);

            return back()->with('success', 'Clocked out successfully at ' . Carbon::parse($currentTime)->format('h:i A') . '. Total hours: ' . $workHours . ' hrs.');
        }

        return back()->with('error', 'Invalid action.');
    }

    public function storeManual(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'date' => ['required', 'date'],
            'check_in' => ['required', 'date_format:H:i'],
            'check_out' => ['nullable', 'date_format:H:i'],
            'status' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $checkInFormatted = $validated['check_in'] . ':00';
        $checkOutFormatted = $validated['check_out'] ? $validated['check_out'] . ':00' : null;

        $workHours = 0;
        $overtimeMinutes = 0;
        $lateMinutes = 0;

        if ($checkOutFormatted) {
            $in = Carbon::parse($validated['date'] . ' ' . $checkInFormatted);
            $out = Carbon::parse($validated['date'] . ' ' . $checkOutFormatted);
            $workHours = max(0, round($out->diffInMinutes($in) / 60, 2));

            $regularEnd = Carbon::parse($validated['date'] . ' 17:00:00');
            if ($out->gt($regularEnd)) {
                $overtimeMinutes = $out->diffInMinutes($regularEnd);
            }
        }

        $regularStart = Carbon::parse($validated['date'] . ' 08:00:00');
        $inObj = Carbon::parse($validated['date'] . ' ' . $checkInFormatted);
        if ($inObj->gt($regularStart)) {
            $lateMinutes = $inObj->diffInMinutes($regularStart);
        }

        Attendance::updateOrCreate(
            ['user_id' => $validated['user_id'], 'date' => $validated['date']],
            [
                'check_in' => $checkInFormatted,
                'check_out' => $checkOutFormatted,
                'status' => $validated['status'],
                'work_hours' => $workHours,
                'overtime_minutes' => $overtimeMinutes,
                'late_minutes' => $lateMinutes,
                'scan_method' => 'manual',
                'notes' => $validated['notes'] ?? 'Manual entry by Administrator',
            ]
        );

        return back()->with('success', 'Attendance record saved successfully.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return back()->with('success', 'Attendance record deleted.');
    }
}
