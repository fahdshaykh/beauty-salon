<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\User;
use App\Models\EmployeeAttendance;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class EmployeeAttendanceController extends Controller
{
    public function employee_attendance()
    {
        if (! (auth()->user() && auth()->user()->can('create attendance'))) {
            abort(403);
        }

        // $users = User::where('company_id', auth()->user()->company_id)
        //     ->notCheckedInToday()
        //     ->get(['id', 'name']);
        $users = User::activeInCompany()
                    ->when(!auth()->user()->hasRole('admin'), fn($q) => $q->where('id', auth()->user()->id))
                    ->get();

        return view('employee_attendance.create', compact('users')); // Pass data to the view
    }

    public function attendance()
    {
        if (! (auth()->user() && auth()->user()->can('view attendance'))) {
            abort(403);
        }

        $company_id = auth()->user()->company_id;
        $employees = EmployeeAttendance::where('company_id', $company_id)
            ->whereDate('created_at', today()) // Filter only today's records
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('employee_attendance.today_attendance', compact('employees'));
    }

    public function store(Request $request)
    {
        if (! (auth()->user() && auth()->user()->can('create attendance'))) {
            abort(403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);

        // ---------- 1. TODAY ----------
        $today = now()->startOfDay();               // 00:00:00 today
        $tomorrow = $today->clone()->addDay();      // 00:00:00 tomorrow

        // ---------- 2. CHECK FOR EXISTING RECORD ----------
        $existing = EmployeeAttendance::where('user_id', $user->id)
            ->where('check_in', '>=', $today)
            ->where('check_in', '<', $tomorrow)
            ->first();

        if ($existing) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Attendance for this employee has already been recorded today.');
        }

        // ---------- 3. SCHEDULE ----------
        $currentDay = now()->format('l');   // Monday, Tuesday, …
        $schedule   = WorkSchedule::where('day', $currentDay)->first();

        if (! $schedule) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'No schedule found for today');
        }

        // ---------- 4. CHECK‑IN ----------
        $checkInTime = now();
        $isLate      = $checkInTime->gt(Carbon::parse($schedule->start_time));

        // ---------- 5. CREATE ----------
        EmployeeAttendance::create([
            'user_id'           => $user->id,
            'check_in'          => $checkInTime,
            'status'            => $request->status,
            'reason'            => $request->reason ?? null,
            'check_in_location' => request()->ip(),
            'is_late'           => $isLate,
            'company_id'        => auth()->user()->company_id,
        ]);

        return redirect()
            ->route('attendance')
            ->with('success', 'User attendance created successfully');
    }

    public function updateExitTime(Request $request)
    {
        if (! (auth()->user() && auth()->user()->can('edit attendance'))) {
            abort(403);
        }

        $request->validate([
            'user_id' => 'required|exists:employee_attendances,user_id',
            'check_out_time' => 'required|date_format:H:i',
        ]);

        $attendance = EmployeeAttendance::where('user_id', $request->user_id)
                                        ->whereDate('created_at', now()->toDateString())
                                        ->first();

        if (!$attendance) {
            return response()->json(['success' => false, 'message' => 'Attendance record not found']);
        }

        $checkInTime = Carbon::parse($attendance->check_in)->setDate(now()->year, now()->month, now()->day);
        $checkOutTime = Carbon::createFromFormat('H:i', $request->check_out_time)->setDate(now()->year, now()->month, now()->day);

        if ($checkOutTime->lessThanOrEqualTo($checkInTime)) {
            return response()->json([
                'success' => false,
                'message' => 'Check-out time must be greater than check-in time'
            ]);
        }

        $currentDay = now()->format('l');
        $schedule = WorkSchedule::where('day', $currentDay)->first();

        if (!$schedule) {
            return response()->json(['success' => false, 'message' => 'Work schedule not found']);
        }

        $scheduleEndTime = Carbon::parse($schedule->end_time)->setDate(now()->year, now()->month, now()->day);
        $overtimeMinutes = max(0, $checkOutTime->diffInMinutes($scheduleEndTime));

        $attendance->update([
            'check_out' => $checkOutTime,
            'check_out_location' => $request->ip(),
            'transport_assistance' => $request->transport_assistance ? 'yes' : 'no',
            'overtime_minutes' => $overtimeMinutes,
        ]);

        return response()->json(['success' => true, 'message' => 'Exit time updated successfully']);
    }



    public function work_hours(Request $request)
    {
        if (! (auth()->user() && auth()->user()->can('view work hours'))) {
            abort(403);
        }

        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        $users = User::where('company_id', auth()->user()->company_id)->get();

        $query = EmployeeAttendance::query();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->where('check_in', '>=', $request->date_from);
        } else {
            $query->where('check_in', '>=', $start);
        }

        if ($request->filled('date_to')) {
            // Better: use check_out <= end of day
            $query->where('check_out', '<=', $request->date_to . ' 23:59:59');
        } else {
            $query->where('check_out', '<=', $end);
        }

                // get rows per page if passed with request
        if (request('rows')) {

            $rows = 500 < intval(request('rows')) ? 500 : intval(request('rows'));
            $rows = 5 > $rows ? 5 : $rows;
            Auth::user()->setSettings(['work_hours_rows_per_page' => $rows]);
        }
        // otherwise retrieve from user's settings
        $rows = Auth::user()->getSetting('work_hours_rows_per_page', auth()->user()->default_rows);

        $workHours = $query->with('user')->orderBy('created_at', 'desc')->paginate($rows);

        return view('employee_attendance.work_hours', compact('workHours', 'users', 'rows'));
    }

}
