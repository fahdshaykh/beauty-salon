<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkSchedule;

class WorkScheduleController extends Controller
{
    public function index(Request $request)
    {

        // Fetch schedules for the logged-in user's company, ordered by day
        $schedules = WorkSchedule::where('company_id', auth()->user()->company_id)
            ->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
            ->get();

        // Optional: If you want to use $dayOrder map for custom sorting
        // $schedules = $schedules->sortBy(function ($schedule) use ($dayOrder) {
        //     return $dayOrder[$schedule->day] ?? 99;
        // })->values();

        return view('employee_attendance.work_schedule', compact('schedules'));
    }

    public function edit($id)
    {
        $schedule = WorkSchedule::where('id', $id)
            ->where('company_id', auth()->user()->company_id)
            ->firstOrFail();
        return response()->json([
            'id'         => $schedule->id,
            'day'        => $schedule->day,
            'start_time' => $schedule->start_time,
            'end_time'   => $schedule->end_time,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (! auth()->user()->can('edit work schedule')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'start_time' => 'required',
            'end_time'   => 'required|after:start_time',
        ]);

        $schedule = WorkSchedule::where('id', $id)
            ->where('company_id', auth()->user()->company_id)
            ->firstOrFail();

        $schedule->update([
            'start_time' => $request->start_time . ':00',
            'end_time'   => $request->end_time . ':00',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Schedule updated successfully',
            'data'    => $schedule
        ]);
    }

    public function categoryStatus(Request $request)
    {
        $workSchedule = WorkSchedule::where('id', $request->id)
            ->where('company_id', auth()->user()->company_id)
            ->firstOrFail();

        $workSchedule->is_active = $request->status;
        $workSchedule->save();

        return response()->json(['success'=>'Status changed successfully.']);
    }
}
