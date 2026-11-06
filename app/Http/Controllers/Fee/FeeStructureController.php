<?php

namespace App\Http\Controllers\Fee;

use App\Http\Controllers\Controller;
use App\Models\ClassFeeStructure;
use App\Models\SchoolClass;
use App\Models\Term;
use App\Models\GradeLevel;
use App\Models\FeeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Concerns\AuthorizesCrud;
use Illuminate\Routing\Controllers\HasMiddleware;
class FeeStructureController extends Controller implements HasMiddleware
{
    use AuthorizesCrud;
    protected static string $permissionModule = 'fee_structures';

    public function index(Request $request)
    {
        try {
            $gradeLevels = GradeLevel::orderBy('sort_order')->get();
            $terms = Term::with('academicYear')->orderBy('term_number')->get();
            $feeTypes = FeeType::orderBy('name')->get();

            // Default to an empty paginator if no Grade Level is selected
            $feeStructures = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15, 1, [
                'path' => $request->url(), 
                'query' => $request->query()
            ]);

            // Only fetch data if a Grade Level is selected
            if ($request->filled('grade_level_id')) {
                
                $query = ClassFeeStructure::with(['schoolClass.gradeLevel', 'term', 'feeType'])
                    ->whereHas('schoolClass', function($q) use ($request) {
                        $q->where('grade_level_id', $request->input('grade_level_id'));
                    });

                if ($request->filled('term_id')) {
                    $query->where('term_id', $request->input('term_id'));
                }
                if ($request->filled('fee_type_id')) {
                    $query->where('fee_type_id', $request->input('fee_type_id'));
                }

                // Group by Term + Fee Type
                $allStructures = $query->latest()->get();
                $grouped = $allStructures->groupBy(function($item) {
                    return $item->term_id . '-' . $item->fee_type_id;
                })->map(function($group) {
                    return $group->first();
                })->values();

                // Paginate the grouped results
                $page = $request->get('page', 1);
                $perPage = 15;
                $feeStructures = new \Illuminate\Pagination\LengthAwarePaginator(
                    $grouped->forPage($page, $perPage)->values(),
                    $grouped->count(),
                    $perPage,
                    $page,
                    ['path' => $request->url(), 'query' => $request->query()]
                );
            }

            return view('fee.fee_structures.index', compact('feeStructures', 'gradeLevels', 'terms', 'feeTypes'));

        } catch (\Exception $e) {
            Log::error('Error fetching fee structures: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load fee structures.');
        }
    }

    public function create(Request $request)
    {
        try {
            $gradeLevels = GradeLevel::orderBy('sort_order')->get();
            $terms = Term::with('academicYear')->get();
            $feeTypes = FeeType::where('is_active', true)->get();
            
            $selectedGradeId = $request->input('grade_level_id'); // From the index page link
            $existingFees = collect(); // Empty collection for create

            return view('fee.fee_structures.form', compact('gradeLevels', 'terms', 'feeTypes', 'selectedGradeId', 'existingFees'));

        } catch (\Exception $e) {
            Log::error('Error loading create form: ' . $e->getMessage());
            return redirect()->route('fee-structures.index')->with('error', 'Could not load the form.');
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'grade_level_id'=> ['required', 'exists:grade_levels,id'],
            'term_id'       => ['required', 'exists:terms,id'],
            'amounts'       => ['required', 'array'],
            'amounts.*'     => ['nullable', 'numeric', 'min:0'],
            'is_optional'   => ['nullable', 'boolean'],
            'due_date'      => ['nullable', 'date'],
            'notes'         => ['nullable', 'string', 'max:255'],
        ]);

        // Filter out empty inputs
        $submittedAmounts = array_filter($validated['amounts'], function($amount) {
            return $amount !== null && $amount !== '';
        });

        if (empty($submittedAmounts)) {
            return back()->withInput()->with('error', 'Please enter an amount for at least one fee type.');
        }

        try {
            $classes = SchoolClass::where('grade_level_id', $validated['grade_level_id'])->get();

            if ($classes->isEmpty()) {
                return back()->withInput()->with('error', 'No classes/streams exist for this Grade Level yet.');
            }

            foreach ($classes as $class) {
                foreach ($submittedAmounts as $feeTypeId => $amount) {
                    ClassFeeStructure::updateOrCreate(
                        [
                            'class_id'    => $class->id,
                            'term_id'     => $validated['term_id'],
                            'fee_type_id' => $feeTypeId,
                        ],
                        [
                            'amount'      => $amount,
                            'is_optional' => $request->boolean('is_optional'),
                            'due_date'    => $validated['due_date'] ?? null,
                            'notes'       => $validated['notes'] ?? null,
                        ]
                    );
                }
            }

            return redirect()->route('fee-structures.index', ['grade_level_id' => $validated['grade_level_id']])
                             ->with('success', 'Fee Structures saved successfully for all streams.');

        } catch (\Exception $e) {
            Log::error('Error creating fee structure: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to save fee structures.');
        }
    }

    public function edit(ClassFeeStructure $feeStructure)
    {
        try {
            $gradeLevels = GradeLevel::orderBy('sort_order')->get();
            $terms = Term::with('academicYear')->get();
            $feeTypes = FeeType::where('is_active', true)->get();

            $gradeLevelId = $feeStructure->schoolClass->grade_level_id;
            $termId = $feeStructure->term_id;

            // Fetch existing amounts for this grade level and term to pre-fill the form
            $existingFees = ClassFeeStructure::where('term_id', $termId)
                ->whereHas('schoolClass', function($q) use ($gradeLevelId) {
                    $q->where('grade_level_id', $gradeLevelId);
                })
                ->get()
                ->keyBy('fee_type_id');

            $selectedGradeId = $gradeLevelId;
            $selectedTermId = $termId;

            return view('fee.fee_structures.form', compact('feeStructure', 'gradeLevels', 'terms', 'feeTypes', 'existingFees', 'selectedGradeId', 'selectedTermId'));

        } catch (\Exception $e) {
            Log::error('Error loading edit form: ' . $e->getMessage());
            return redirect()->route('fee-structures.index')->with('error', 'Could not load the edit form.');
        }
    }

    public function update(Request $request, ClassFeeStructure $feeStructure)
    {
        $validated = $request->validate([
            'grade_level_id'=> ['required', 'exists:grade_levels,id'],
            'term_id'       => ['required', 'exists:terms,id'],
            'amounts'       => ['required', 'array'],
            'amounts.*'     => ['nullable', 'numeric', 'min:0'],
            'is_optional'   => ['nullable', 'boolean'],
            'due_date'      => ['nullable', 'date'],
            'notes'         => ['nullable', 'string', 'max:255'],
        ]);

        $submittedAmounts = array_filter($validated['amounts'], function($amount) {
            return $amount !== null && $amount !== '';
        });

        if (empty($submittedAmounts)) {
            return back()->withInput()->with('error', 'Please enter an amount for at least one fee type.');
        }

        try {
            $oldGradeLevelId = $feeStructure->schoolClass->grade_level_id;
            $classes = SchoolClass::where('grade_level_id', $validated['grade_level_id'])->get();

            // If the Grade Level or Term changed, delete the old records to prevent orphans
            if ($oldGradeLevelId != $validated['grade_level_id'] || $feeStructure->term_id != $validated['term_id']) {
                $oldClassIds = SchoolClass::where('grade_level_id', $oldGradeLevelId)->pluck('id');
                ClassFeeStructure::whereIn('class_id', $oldClassIds)
                    ->where('term_id', $feeStructure->term_id)
                    ->delete();
            }

            foreach ($classes as $class) {
                // Delete existing records for this class/term that were cleared out (left blank)
                ClassFeeStructure::where('class_id', $class->id)
                    ->where('term_id', $validated['term_id'])
                    ->whereNotIn('fee_type_id', array_keys($submittedAmounts))
                    ->delete();

                // Create or update the provided amounts
                foreach ($submittedAmounts as $feeTypeId => $amount) {
                    ClassFeeStructure::updateOrCreate(
                        [
                            'class_id'    => $class->id,
                            'term_id'     => $validated['term_id'],
                            'fee_type_id' => $feeTypeId,
                        ],
                        [
                            'amount'      => $amount,
                            'is_optional' => $request->boolean('is_optional'),
                            'due_date'    => $validated['due_date'] ?? null,
                            'notes'       => $validated['notes'] ?? null,
                        ]
                    );
                }
            }

            return redirect()->route('fee-structures.index', ['grade_level_id' => $validated['grade_level_id']])
                             ->with('success', 'Fee Structures updated successfully across all streams.');

        } catch (\Exception $e) {
            Log::error('Error updating fee structure: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update fee structures.');
        }
    }



    public function destroy(ClassFeeStructure $feeStructure)
    {
        try {
            // Find all classes (streams) under the same Grade Level
            $classIds = SchoolClass::where('grade_level_id', $feeStructure->schoolClass->grade_level_id)->pluck('id');
            
            // Delete the fee structure for ALL streams in this class
            ClassFeeStructure::whereIn('class_id', $classIds)
                ->where('term_id', $feeStructure->term_id)
                ->where('fee_type_id', $feeStructure->fee_type_id)
                ->delete();

            return redirect()->route('fee-structures.index')->with('success', 'Fee Structure deleted across all streams.');

        } catch (\Exception $e) {
            Log::error('Error deleting fee structure: ' . $e->getMessage());
            return redirect()->route('fee-structures.index')->with('error', 'Failed to delete fee structure.');
        }
    }
}