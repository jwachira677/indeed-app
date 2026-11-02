<?php

namespace App\Http\Controllers;

use App\Models\GradeLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GradeLevelController extends Controller
{
    public function index()
    {
        try {
            $gradeLevels = GradeLevel::orderBy('sort_order')->paginate(15);
            return view('grade-levels.index', compact('gradeLevels'));
        } catch (\Exception $e) {
            Log::error('Error fetching grade levels: ' . $e->getMessage());
            return back()->with('error', 'Failed to load grade levels.');
        }
    }

    public function create()
    {
        return view('grade-levels.form');
    }

    public function store(Request $request)
    {
        //GradeLevelController
        $validated = $request->validate([
            'code'       => ['required', 'string', 'max:10', 'unique:grade_levels,code'],
            'name'       => ['required', 'string', 'max:50'],
            'level_group'=> ['required', 'in:PRE_PRIMARY,LOWER_PRIMARY,UPPER_PRIMARY,JUNIOR_SECONDARY,SENIOR_SECONDARY'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        try {
            GradeLevel::create($validated);
            return redirect()->route('grade-levels.index')->with('success', 'Grade Level created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating grade level: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create grade level.');
        }
    }

    public function edit(GradeLevel $gradeLevel)
    {
        return view('grade-levels.form', compact('gradeLevel'));
    }

    public function update(Request $request, GradeLevel $gradeLevel)
    {
        $validated = $request->validate([
            'code'       => ['required', 'string', 'max:10', 'unique:grade_levels,code,' . $gradeLevel->id],
            'name'       => ['required', 'string', 'max:50'],
            'level_group'=> ['required', 'in:PRE_PRIMARY,LOWER_PRIMARY,UPPER_PRIMARY,JUNIOR_SECONDARY,SENIOR_SECONDARY'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        try {
            $gradeLevel->update($validated);
            return redirect()->route('grade-levels.index')->with('success', 'Grade Level updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating grade level: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update grade level.');
        }
    }

    public function destroy(GradeLevel $gradeLevel)
    {
        try {
            // Prevent deletion if classes are attached
            if ($gradeLevel->classes()->exists()) {
                return back()->with('error', 'Cannot delete: This grade level has classes assigned to it.');
            }
            
            $gradeLevel->delete();
            return redirect()->route('grade-levels.index')->with('success', 'Grade Level deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting grade level: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete grade level.');
        }
    }
}