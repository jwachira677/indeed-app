@extends('layouts.app')

@section('title', isset($gradeLevel) ? 'Edit Grade Level' : 'Create Grade Level')

@section('content')
<div class="container-fluid">
    <!-- Header / Back Button -->
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('grade-levels.index') }}" class="btn btn-link text-decoration-none me-3">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h4 class="fw-bold mb-0">
            {{ isset($gradeLevel) ? 'Edit Grade Level: ' . $gradeLevel->name : 'Create New Grade Level' }}
        </h4>
    </div>

    <div class="row">
        <!-- Form inside a Card -->
        <div class="col-md-10 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    
                    <form method="POST" action="{{ isset($gradeLevel) ? route('grade-levels.update', $gradeLevel->id) : route('grade-levels.store') }}">
                        @csrf
                        @if(isset($gradeLevel)) @method('PUT') @endif

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Code *</label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $gradeLevel->code ?? '') }}" placeholder="e.g. G7" required>
                                @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Name *</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $gradeLevel->name ?? '') }}" placeholder="e.g. Grade 7" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Level Group *</label>
                                <select name="level_group" class="form-select @error('level_group') is-invalid @enderror" required>
                                    <option value="">Select Group</option>
                                    @foreach(\App\Models\GradeLevel::getLevelGroups() as $val => $label)
                                        <option value="{{ $val }}" {{ old('level_group', $gradeLevel->level_group ?? '') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('level_group') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Sort Order *</label>
                                <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $gradeLevel->sort_order ?? 0) }}" required>
                                @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('grade-levels.index') }}" class="btn btn-light border">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i> {{ isset($gradeLevel) ? 'Update Grade Level' : 'Create Grade Level' }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection