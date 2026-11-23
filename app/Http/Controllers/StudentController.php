<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Term;
use App\Models\TransportRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Concerns\AuthorizesCrud;
use Illuminate\Routing\Controllers\HasMiddleware;

class StudentController extends Controller implements HasMiddleware
{
    use AuthorizesCrud;
    protected static string $permissionModule = 'students';
    public function index(Request $request)
    {
        $query = Student::with([
            'currentEnrollment.schoolClass.gradeLevel', 
            'currentEnrollment.schoolClass.stream'
        ])->orderBy('last_name');

        // Apply Class Filter (Now filtering by class_name instead of class_id)
        if ($request->filled('class_name')) {
            $query->whereHas('currentEnrollment.schoolClass', function ($q) use ($request) {
                $q->where('class_name', $request->input('class_name'));
            });
        }

        // Apply Stream Filter
        if ($request->filled('stream_id')) {
            $query->whereHas('currentEnrollment.schoolClass', function ($q) use ($request) {
                $q->where('stream_id', $request->input('stream_id'));
            });
        }

        $students = $query->paginate(15)->withQueryString();
        
        // Get UNIQUE class names to prevent duplicates in the dropdown
        $classes = SchoolClass::select('class_name')->distinct()->orderBy('class_name')->get();
        
        // Get all streams for the dropdown
        $streams = Stream::orderBy('name')->get();

        return view('students.index', compact('students', 'classes', 'streams'));
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
public function fees(Request $request, Student $student)
{
    // 1. REMOVED the join() - just use standard Eloquent ordering
    $terms = Term::with('academicYear')
        ->orderBy('academic_year_id', 'desc')
        ->orderBy('term_number', 'asc')
        ->get();
    
    if ($request->filled('term_id')) {
        $termId = $request->input('term_id');
    } else {
        // Try to find a term that covers today's date
        $dateTerm = Term::where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        // Fallback: Find the first term of the current academic year
        $currentYearTerm = Term::whereHas('academicYear', function($q) {
            $q->where('is_current', true);
        })->orderBy('term_number')->first();

        // Fallback: Get the most recent term available
        $fallbackTerm = $terms->first();

        $termId = $dateTerm?->id ?? $currentYearTerm?->id ?? $fallbackTerm?->id;
    }

    if (!$termId) {
        return back()->with('error', 'No terms have been created yet.');
    }

    $selectedTerm = $terms->firstWhere('id', $termId) ?? Term::find($termId);

    $student->load(['currentEnrollment.schoolClass', 'transportRoute']);
    $fees = $student->getFeeBreakdown($termId);

    $compulsoryTotal = $fees->where('is_optional', false)->sum('amount');
    $optionalTotal   = $fees->where('is_optional', true)->sum('amount');
    $grandTotal      = $fees->sum('amount');

    return view('fee.bills.index', compact(
        'student', 
        'terms', 
        'termId', 
        'selectedTerm', 
        'fees', 
        'compulsoryTotal', 
        'optionalTotal', 
        'grandTotal'
    ));
}
}