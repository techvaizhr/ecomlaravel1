<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Toastr;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = EmployeeAttendance::with('employee', 'markedBy')->orderBy('attendance_date', 'DESC');

        // Search
        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->whereHas('employee', function($eq) use ($keyword) {
                $eq->where('name', 'LIKE', '%' . $keyword . '%')
                   ->orWhere('employee_id', 'LIKE', '%' . $keyword . '%');
            });
        }

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('attendance_date', $request->date);
        }

        // Filter by month
        if ($request->filled('month')) {
            $query->whereMonth('attendance_date', Carbon::parse($request->month)->month)
                  ->whereYear('attendance_date', Carbon::parse($request->month)->year);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Per page
        $perPage = $request->get('per_page', 30);
        if ($perPage === 'all' || $perPage == -1) {
            $perPage = max(EmployeeAttendance::count(), 1);
        } else {
            $perPage = max((int)$perPage, 10);
        }

        $attendances = $query->paginate($perPage)->withQueryString();
        $employees = Employee::where('status', 'active')->orderBy('name')->get();

        $stats = [
            'total_count'    => EmployeeAttendance::count(),
            'present_count'  => EmployeeAttendance::where('status', 'present')->count(),
            'absent_count'   => EmployeeAttendance::where('status', 'absent')->count(),
            'late_half_count'=> EmployeeAttendance::whereIn('status', ['late', 'half_day'])->count(),
        ];

        return view('backEnd.attendances.index', compact('attendances', 'employees', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::where('status', 'active')->orderBy('name')->get();
        return view('backEnd.attendances.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'attendance_date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => 'required|in:present,absent,late,half_day,holiday',
            'notes' => 'nullable|string',
        ]);

        // Check if attendance already exists for this date
        $existing = EmployeeAttendance::where('employee_id', $request->employee_id)
            ->whereDate('attendance_date', $request->attendance_date)
            ->first();

        if ($existing) {
            Toastr::error('Attendance already marked for this date!');
            return back();
        }

        EmployeeAttendance::create([
            'employee_id' => $request->employee_id,
            'attendance_date' => $request->attendance_date,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'status' => $request->status,
            'notes' => $request->notes,
            'marked_by' => Auth::id(),
        ]);

        Toastr::success('Attendance marked successfully!');
        return redirect()->route('admin.attendances.index');
    }

    /**
     * Bulk attendance marking
     */
    public function bulkMark(Request $request)
    {
        $request->validate([
            'attendance_date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.employee_id' => 'required|exists:employees,id',
            'attendances.*.status' => 'required|in:present,absent,late,half_day,holiday',
            'attendances.*.check_in' => 'nullable|date_format:H:i',
            'attendances.*.check_out' => 'nullable|date_format:H:i',
        ]);

        $date = $request->attendance_date;
        $marked = 0;
        $skipped = 0;

        foreach ($request->attendances as $attendance) {
            // Check if already exists
            $existing = EmployeeAttendance::where('employee_id', $attendance['employee_id'])
                ->whereDate('attendance_date', $date)
                ->first();

            if ($existing) {
                $skipped++;
                continue;
            }

            EmployeeAttendance::create([
                'employee_id' => $attendance['employee_id'],
                'attendance_date' => $date,
                'check_in' => $attendance['check_in'] ?? null,
                'check_out' => $attendance['check_out'] ?? null,
                'status' => $attendance['status'],
                'marked_by' => Auth::id(),
            ]);

            $marked++;
        }

        Toastr::success("Attendance marked for {$marked} employees. {$skipped} skipped (already marked).");
        return redirect()->route('admin.attendances.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $attendance = EmployeeAttendance::with('employee')->findOrFail($id);
        return view('backEnd.attendances.edit', compact('attendance'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $attendance = EmployeeAttendance::findOrFail($id);

        $request->validate([
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => 'required|in:present,absent,late,half_day,holiday',
            'notes' => 'nullable|string',
        ]);

        $attendance->update($request->all());

        Toastr::success('Attendance updated successfully!');
        return redirect()->route('admin.attendances.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $attendance = EmployeeAttendance::findOrFail($id);
        $attendance->delete();

        Toastr::success('Attendance deleted successfully!');
        return redirect()->route('admin.attendances.index');
    }
}
