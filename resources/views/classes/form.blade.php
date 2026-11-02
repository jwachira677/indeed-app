@extends('layouts.app')

@section('title', isset($class) ? 'Edit Class' : 'Create Class')

@section('content')
<div class="container-fluid">
    <!-- Header / Back Button -->
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('classes.index') }}" class="btn btn-link text-decoration-none me-3">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h4 class="fw-bold mb-0">
            {{ isset($class) ? 'Edit Class: ' . $class->class_name . ($class->stream ? " - {$class->stream}" : '') : 'Create New Class / Stream' }}
        </h4>
    </div>

    <div class="row">
        <!-- Form inside a Card -->
        <div class="col-md-10 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    
                    <form method="POST" action="{{ isset($class) ? route('classes.update', $class->id) : route('classes.store') }}">
                        @csrf
                        @if(isset($class)) @method('PUT') @endif

                        <div class="row">
                            <!-- Grade Level -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Grade Level *</label>
                                <select name="grade_level_id" class="form-select @error('grade_level_id') is-invalid @enderror" required>
                                    <option value="">Select Grade Level</option>
                                    @foreach($gradeLevels as $level)
                                        <option value="{{ $level->id }}" {{ old('grade_level_id', $class->grade_level_id ?? '') == $level->id ? 'selected' : '' }}>
                                            {{ $level->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('grade_level_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Academic Year -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Academic Year *</label>
                                <select name="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                                    <option value="">Select Academic Year</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ old('academic_year_id', $class->academic_year_id ?? '') == $year->id ? 'selected' : '' }}>
                                            {{ $year->year_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('academic_year_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-label">Stream</label>
                                    <a href="{{ route('streams.create') }}" class="text-primary text-decoration-none" style="font-size: 0.75rem;" target="_blank">
                                        <i class="bi bi-plus-circle"></i> Add New
                                    </a>
                                </div>
                                <select name="stream_id" class="form-select @error('stream_id') is-invalid @enderror">
                                    <option value="">No Stream / Main</option>
                                    @foreach($streams as $stream)
                                        <option value="{{ $stream->id }}" {{ old('stream_id', $class->stream_id ?? '') == $stream->id ? 'selected' : '' }}>
                                            {{ $stream->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('stream_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('classes.index') }}" class="btn btn-light border">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i> {{ isset($class) ? 'Update Class' : 'Create Class' }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection