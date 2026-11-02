@extends('layouts.app')

@section('title', isset($feeStructure) ? 'Edit Fee Structure' : 'Create Fee Structure')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('fee-structures.index', ['grade_level_id' => $selectedGradeId ?? '']) }}" class="btn btn-link text-decoration-none me-3">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h4 class="fw-bold mb-0">{{ isset($feeStructure) ? 'Edit Fee Structure' : 'Create New Fee Structure' }}</h4>
    </div>

    <div class="row">
        <div class="col-md-10 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    
                    <form method="POST" action="{{ isset($feeStructure) ? route('fee-structures.update', $feeStructure->id) : route('fee-structures.store') }}">
                        @csrf
                        @if(isset($feeStructure)) @method('PUT') @endif

                        <div class="row">
                            <!-- Grade Level -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Grade Level *</label>
                                <select name="grade_level_id" class="form-select @error('grade_level_id') is-invalid @enderror" required>
                                    <option value="">Select Grade Level</option>
                                    @foreach($gradeLevels as $grade)
                                        <option value="{{ $grade->id }}" {{ old('grade_level_id', $selectedGradeId ?? '') == $grade->id ? 'selected' : '' }}>
                                            {{ $grade->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-secondary">Applies to all streams under this grade.</small>
                                @error('grade_level_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Term -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Term *</label>
                                <select name="term_id" class="form-select @error('term_id') is-invalid @enderror" required>
                                    <option value="">Select Term</option>
                                    @foreach($terms as $term)
                                        <option value="{{ $term->id }}" {{ old('term_id', $selectedTermId ?? '') == $term->id ? 'selected' : '' }}>
                                            Term {{ $term->term_number }} ({{ $term->academicYear->year_name ?? '' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('term_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <hr class="my-4 text-muted">

                        <!-- Fee Amounts list -->
                        <h6 class="text-uppercase text-secondary fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">Fee Amounts (KES)</h6>
                        <p class="text-muted small mb-4">Enter the amount for each fee type you want to apply. Leave blank to ignore.</p>

                        <div class="row mb-4">
                            @foreach($feeTypes as $type)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">{{ $type->name }} @if($type->is_optional) <span class="badge bg-warning text-dark ms-1" style="font-size:0.65rem;">Opt</span> @endif</label>
                                    <div class="input-group">
                                        <span class="input-group-text">KES</span>
                                        <input type="number" 
                                               step="0.01" 
                                               min="0"
                                               name="amounts[{{ $type->id }}]" 
                                               class="form-control @error('amounts.'.$type->id) is-invalid @enderror" 
                                               value="{{ old('amounts.'.$type->id, $existingFees[$type->id]->amount ?? '') }}" 
                                               placeholder="0.00">
                                        @error('amounts.'.$type->id)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <hr class="my-4 text-muted">

                        <!-- Global Settings for this batch -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="due_date" class="form-label">Due Date (Applies to all above)</label>
                                <input type="date" 
                                       class="form-control @error('due_date') is-invalid @enderror" 
                                       id="due_date" 
                                       name="due_date" 
                                       value="{{ old('due_date', isset($feeStructure->due_date) ? $feeStructure->due_date->format('Y-m-d') : '') }}">
                                @error('due_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3 d-flex align-items-center">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" role="switch" id="is_optional" name="is_optional" value="1" 
                                           {{ old('is_optional', $feeStructure->is_optional ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_optional">Mark all as optional?</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label">Notes (Applies to all above)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="2" maxlength="255"
                                      placeholder="Any additional information...">{{ old('notes', $feeStructure->notes ?? '') }}</textarea>
                            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('fee-structures.index', ['grade_level_id' => $selectedGradeId ?? '']) }}" class="btn btn-light border">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i> {{ isset($feeStructure) ? 'Update Structures' : 'Save Structures' }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection