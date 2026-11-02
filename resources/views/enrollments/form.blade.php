@extends('layouts.app')

@section('title', isset($enrollment) ? 'Edit Enrollment' : 'New Enrollment')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ url()->previous() }}" class="btn btn-link text-decoration-none me-3">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h4 class="fw-bold mb-0">
            {{ isset($enrollment) ? 'Edit Enrollment' : 'Enroll Student' }}
        </h4>
    </div>

    <div class="row">
        <div class="col-md-10 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    
                    <form method="POST" action="{{ isset($enrollment) ? route('enrollments.update', $enrollment->id) : route('enrollments.store') }}">
                        @csrf
                        @if(isset($enrollment)) @method('PUT') @endif

                        <div class="row">
                            <!-- Student Dropdown -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Student *</label>
                                @if(isset($student))
                                    <!-- If a specific student was passed, lock it -->
                                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                                    <input type="text" class="form-control bg-light" value="{{ $student->full_name }} ({{ $student->admission_number }})" readonly>
                                @else
                                    <!-- Otherwise, show dropdown of all students -->
                                    <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                                        <option value="">Select Student</option>
                                        @foreach($students as $s)
                                            <option value="{{ $s->id }}" {{ old('student_id', $enrollment->student_id ?? '') == $s->id ? 'selected' : '' }}>
                                                {{ $s->full_name }} ({{ $s->admission_number }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <!-- Class Dropdown -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Assign to Class *</label>
                                <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                                    <option value="">Select Class</option>
                                    
                                    {{-- Group classes by Academic Year to keep it organized --}}
                                    @foreach($classes->groupBy('academicYear.year_name') as $year => $yearClasses)
                                        <optgroup label="Year: {{ $year }}">
                                            @foreach($yearClasses as $class)
                                                <option value="{{ $class->id }}" {{ old('class_id', $enrollment->class_id ?? '') == $class->id ? 'selected' : '' }}>
                                                    {{ $class->gradeLevel->name ?? 'Grade' }} {{ $class->stream ? "- {$class->stream->name}" : '' }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                @error('class_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Enrollment Date -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Enrollment Date *</label>
                                <input type="date" name="enrollment_date" class="form-control @error('enrollment_date') is-invalid @enderror" value="{{ old('enrollment_date', isset($enrollment->enrollment_date) ? $enrollment->enrollment_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                                @error('enrollment_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status *</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="ACTIVE" {{ old('status', $enrollment->status ?? 'ACTIVE') == 'ACTIVE' ? 'selected' : '' }}>Active</option>
                                    <option value="COMPLETED" {{ old('status', $enrollment->status ?? '') == 'COMPLETED' ? 'selected' : '' }}>Completed</option>
                                    <option value="TRANSFERRED" {{ old('status', $enrollment->status ?? '') == 'TRANSFERRED' ? 'selected' : '' }}>Transferred</option>
                                    <option value="WITHDRAWN" {{ old('status', $enrollment->status ?? '') == 'WITHDRAWN' ? 'selected' : '' }}>Withdrawn</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <hr class="my-4 text-muted">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('enrollments.index') }}" class="btn btn-light border">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i> {{ isset($enrollment) ? 'Update Enrollment' : 'Save Enrollment' }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection