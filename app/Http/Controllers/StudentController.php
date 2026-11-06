<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\SchoolClass;
use App\Models\TransportRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Concerns\AuthorizesCrud;
use Illuminate\Routing\Controllers\HasMiddleware;

class StudentController extends Controller implements HasMiddleware
{
    use AuthorizesCrud;
    protected static string $permissionModule = 'students';
    public function index()
    {
        try {
            // Eager load the active enrollment and class
            $students = Student::with(['currentEnrollment.schoolClass.gradeLevel', 'currentEnrollment.schoolClass.stream'])
                ->orderBy('last_name')
                ->paginate(15);
                
            return view('students.index', compact('students'));
        } catch (\Exception $e) {
            Log::error('Error fetching students: ' . $e->getMessage());
            return back()->with('error', 'Failed to load students.');
        }
    }

    public function create()
    {
        $transportRoutes = TransportRoute::where('is_active', true)->get();
        return view('students.form', compact('transportRoutes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'admission_number' => ['required', 'string', 'max:20', 'unique:students,admission_number'],
            'first_name'       => ['required', 'string', 'max:50'],
            'last_name'        => ['required', 'string', 'max:50'],
            'date_of_birth'    => ['required', 'date'],
            'gender'           => ['required', 'in:MALE,FEMALE,OTHER'],
            
            // Added transport route
            'transport_route_id' => ['nullable', 'exists:transport_routes,id'],

            // Parent/Guardian Logic:
            'mother_name'      => ['nullable', 'string', 'max:100', 'required_without_all:father_name,guardian_name'],
            'father_name'      => ['nullable', 'string', 'max:100', 'required_without_all:mother_name,guardian_name'],
            'guardian_name'    => ['nullable', 'string', 'max:100', 'required_without_all:mother_name,father_name'],
        ], [
            'mother_name.required_without_all' => 'At least one parent or guardian name is required.',
            'father_name.required_without_all' => 'At least one parent or guardian name is required.',
            'guardian_name.required_without_all' => 'You must provide a guardian name if no parents are listed.',
        ]);

        try {
            Student::create($validated);
            return redirect()->route('students.index')->with('success', 'Student created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating student: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create student.');
        }
    }

    public function edit(Student $student)
    {
        $transportRoutes = TransportRoute::where('is_active', true)->get();
        return view('students.form', compact('student', 'transportRoutes'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'admission_number' => ['required', 'string', 'max:20', 'unique:students,admission_number,' . $student->id],
            'first_name'       => ['required', 'string', 'max:50'],
            'last_name'        => ['required', 'string', 'max:50'],
            'date_of_birth'    => ['required', 'date'],
            'gender'           => ['required', 'in:MALE,FEMALE,OTHER'],
            
            // Added transport route
            'transport_route_id' => ['nullable', 'exists:transport_routes,id'],

            // Added Parent/Guardian Logic
            'mother_name'      => ['nullable', 'string', 'max:100', 'required_without_all:father_name,guardian_name'],
            'father_name'      => ['nullable', 'string', 'max:100', 'required_without_all:mother_name,guardian_name'],
            'guardian_name'    => ['nullable', 'string', 'max:100', 'required_without_all:mother_name,father_name'],
        ], [
            'mother_name.required_without_all' => 'At least one parent or guardian name is required.',
            'father_name.required_without_all' => 'At least one parent or guardian name is required.',
            'guardian_name.required_without_all' => 'You must provide a guardian name if no parents are listed.',
        ]);         
        
        try {
            $student->update($validated);
            return redirect()->route('students.index')->with('success', 'Student updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating student: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update student.');
        }
    }

    public function destroy(Student $student)
    {
        try {
            $student->delete();
            return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting student: ' . $e->getMessage());
            return redirect()->route('students.index')->with('error', 'Failed to delete student.');
        }
    }

    // --- ENROLLMENT METHODS ---

    public function enroll(Student $student)
    {
        // Fetch all classes, ordered by Year then Grade
        $classes = SchoolClass::with(['gradeLevel', 'academicYear', 'stream'])
            ->whereHas('academicYear', function($q) {
                $q->orderBy('year_name', 'desc');
            })
            ->get();

        return view('students.enroll', compact('student', 'classes'));
    }

    public function storeEnrollment(Request $request, Student $student)
    {
        $validated = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'enrollment_date' => ['required', 'date'],
        ]);

        try {
            // Optional: Mark any existing ACTIVE enrollment as COMPLETED or TRANSFERRED
            StudentEnrollment::where('student_id', $student->id)
                ->where('status', 'ACTIVE')
                ->update(['status' => 'TRANSFERRED']);

            // Create the new enrollment
            StudentEnrollment::create([
                'student_id' => $student->id,
                'class_id' => $validated['class_id'],
                'enrollment_date' => $validated['enrollment_date'],
                'status' => 'ACTIVE',
            ]);

            return redirect()->route('students.index')->with('success', 'Student successfully enrolled in the class.');
        } catch (\Exception $e) {
            Log::error('Error enrolling student: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to enroll student.');
        }
    }
}