<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\User;
use App\Models\EmployeeAttendance;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class EmployeeAttendanceController extends Controller
{
    public function employee_attendance()
    {
        $users = User::where('company_id', auth()->user()->company_id)
            ->notCheckedInToday()
            ->get(['id', 'name']);

        return view('employee_attendance.create', compact('users')); // Pass data to the view
    }

    public function attendance()
    {
        $company_id = auth()->user()->company_id;
        $employees = EmployeeAttendance::where('company_id', $company_id)
            ->whereDate('created_at', today()) // Filter only today's records
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('employee_attendance.today_attendance', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);
    
        $user = User::findOrFail($request->user_id);
    
        $currentDay = now()->format('l');
        $schedule = WorkSchedule::where('day', $currentDay)->first();
    
        if (!$schedule) {
            return response()->json(['message' => 'No schedule found for today'], 400);
        }
    
        $checkInTime = now();
        $isLate = $checkInTime->gt(Carbon::parse($schedule->start_time));
    
        EmployeeAttendance::create([
            'user_id' => $user->id,
            'check_in' => $checkInTime,
            'status' => $request->status,
            'reason' => $request->reason ?? null,
            'check_in_location' => request()->ip(),
            'is_late' => $isLate,
            'company_id' => auth()->user()->company_id,
        ]);
    
        return redirect()->route('attendance')->with('success', 'user attendence created successfully');
    }

    public function updateExitTime(Request $request)
    {
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
        // Get the current month and year
        $start = now()->startOfMonth(); // First day of the current month
        $end = now()->endOfMonth(); // Last day of the current month

        $users = User::where('company_id', auth()->user()->company_id)->get();

        $query = EmployeeAttendance::query();

        // Apply filters based on user input or default to current month
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        // 1. If 'date_from' and 'date_to' are provided, use them, otherwise default to current month
        if ($request->has('date_from') && $request->date_from != '') {
            $query->where('check_in', '>=', $request->date_from);
        } else {
            $query->where('check_in', '>=', $start); // Default to current month's start
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->where('check_out', '<=', $request->date_to);
        } else {
            $query->where('check_out', '<=', $end); // Default to current month's end
        }

        // Fetch data for the current month or filtered dates
        $workHours = $query->with('user')->get();

        // Pass the data to the view
        return view('employee_attendance.work_hours', compact('workHours', 'users'));
    }

}
