<?php

namespace App\Http\Controllers;

use App\Models\StudentEnrollment;
use App\Models\Student;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Concerns\AuthorizesCrud;
use Illuminate\Routing\Controllers\HasMiddleware;
class EnrollmentController extends Controller implements HasMiddleware
{
    use AuthorizesCrud;
    protected static string $permissionModule = 'enrollments';

    public function index()
    {
        try {
            $enrollments = StudentEnrollment::with(['student', 'schoolClass.gradeLevel', 'schoolClass.stream'])
                ->latest()
                ->paginate(15);
            return view('enrollments.index', compact('enrollments'));
        } catch (\Exception $e) {
            Log::error('Error fetching enrollments: ' . $e->getMessage());
            return back()->with('error', 'Failed to load enrollments.');
        }
    }

    public function create(?Student $student = null)
    {
        // Fetch all students (in case no specific student is selected)
        $students = Student::orderBy('first_name')->get();
        
        // Fetch all classes for the dropdown
        $classes = SchoolClass::with(['gradeLevel', 'academicYear', 'stream'])
            ->orderBy('academic_year_id', 'desc')
            ->get();

        return view('enrollments.form', compact('students', 'classes', 'student'));
    }

    public function store(Request $request, ?Student $student = null)
    {
        // If the student is passed via URL (from the button), force the student_id
        if ($student) {
            $request->merge(['student_id' => $student->id]);
        }

        $validated = $request->validate([
            'student_id'      => ['required', 'exists:students,id'],
            'class_id'        => ['required', 'exists:classes,id'],
            'enrollment_date' => ['required', 'date'],
            'status'          => ['required', 'in:ACTIVE,COMPLETED,TRANSFERRED,WITHDRAWN'],
        ]);

        try {
            if ($validated['status'] === 'ACTIVE') {
                StudentEnrollment::where('student_id', $validated['student_id'])
                    ->where('status', 'ACTIVE')
                    ->update(['status' => 'TRANSFERRED']);
            }

            StudentEnrollment::create($validated);

            return redirect()->route('students.index')->with('success', 'Student enrolled successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating enrollment: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to record enrollment.');
        }
    }

    public function edit(StudentEnrollment $enrollment)
    {
        $students = Student::orderBy('first_name')->get();
        $classes = SchoolClass::with(['gradeLevel', 'academicYear', 'stream'])
            ->orderBy('academic_year_id', 'desc')
            ->get();

        return view('enrollments.form', compact('enrollment', 'students', 'classes'));
    }

    public function update(Request $request, StudentEnrollment $enrollment)
    {
        $validated = $request->validate([
            'student_id'      => ['required', 'exists:students,id'],
            'class_id'        => ['required', 'exists:classes,id'],
            'enrollment_date' => ['required', 'date'],
            'status'          => ['required', 'in:ACTIVE,COMPLETED,TRANSFERRED,WITHDRAWN'],
        ]);

        try {
            $enrollment->update($validated);
            return redirect()->route('enrollments.index')->with('success', 'Enrollment updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating enrollment: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update enrollment.');
        }
    }

    public function destroy(StudentEnrollment $enrollment)
    {
        try {
            $enrollment->delete();
            return redirect()->route('enrollments.index')->with('success', 'Enrollment deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting enrollment: ' . $e->getMessage());
            return redirect()->route('enrollments.index')->with('error', 'Failed to delete enrollment.');
        }
    }
}