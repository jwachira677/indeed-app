<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\GradeLevel;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Stream;
use App\Http\Controllers\Concerns\AuthorizesCrud;
use Illuminate\Routing\Controllers\HasMiddleware;

class SchoolClassController extends Controller implements HasMiddleware
{
    use AuthorizesCrud;
    protected static string $permissionModule = 'classes';
    public function index()
    {
        try {
            $classes = SchoolClass::with(['gradeLevel', 'academicYear'])->latest()->paginate(15);
            return view('classes.index', compact('classes'));
        } catch (\Exception $e) {
            Log::error('Error fetching classes: ' . $e->getMessage());
            return back()->with('error', 'Failed to load classes.');
        }
    }

    public function create()
    {
        $gradeLevels = GradeLevel::orderBy('sort_order')->get();
        $academicYears = AcademicYear::orderBy('year_name', 'desc')->get();
        $streams = Stream::orderBy('name')->get();
        return view('classes.form', compact('gradeLevels', 'academicYears', 'streams'));
    }

    public function store(Request $request)
    {
        // 1. Validate the request
        $validated = $request->validate([
            'grade_level_id'   => ['required', 'exists:grade_levels,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'stream_id'        => ['nullable', 'exists:streams,id'],
        ]);

        $streamId = $validated['stream_id'] ?? null;
        $grade    = GradeLevel::findOrFail($validated['grade_level_id']);

        // 2. Check for duplicates before trying to insert
        $exists = SchoolClass::where('grade_level_id', $grade->id)
            ->where('academic_year_id', $validated['academic_year_id'])
            ->where('stream_id', $streamId)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'This class/stream already exists for the selected year.');
        }

        // 3. Create the class
        try {
            SchoolClass::create([
                'grade_level_id'   => $grade->id,
                'academic_year_id' => $validated['academic_year_id'],
                'class_name'       => $grade->name, // Automatically sets "Grade 1"
                'stream_id'        => $streamId,    // Nullable (e.g., "Blue" or null)
            ]);
            
            return redirect()->route('classes.index')->with('success', 'Class created successfully.');
            
        } catch (\Exception $e) {
            Log::error('Error creating class: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create class. Please try again.');
        }
    }

    public function edit(SchoolClass $class)
    {
        $gradeLevels = GradeLevel::orderBy('sort_order')->get();
        $academicYears = AcademicYear::orderBy('year_name', 'desc')->get();
        $streams = Stream::orderBy('name')->get(); 
        return view('classes.form', compact('class', 'gradeLevels', 'academicYears', 'streams'));
    }

    public function update(Request $request, SchoolClass $class)
    {
        $validated = $request->validate([
            'grade_level_id'   => ['required', 'exists:grade_levels,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'class_name'       => ['required', 'string', 'max:50'],
            'stream_id'        => ['nullable', 'exists:streams,id'],
        ]);

        try {
            $class->update([
                'grade_level_id'   => $validated['grade_level_id'],
                'academic_year_id' => $validated['academic_year_id'],
                'class_name'       => $validated['class_name'],
                'stream_id'        => $validated['stream_id'] ?? null,
            ]);

            return redirect()->route('classes.index')->with('success', 'Class updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating class: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update class.');
        }
    }

    public function destroy(SchoolClass $class)
    {
        try {
            $class->delete();
            return redirect()->route('classes.index')->with('success', 'Class deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting class: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete class. It might have students or fees attached.');
        }
    }
}