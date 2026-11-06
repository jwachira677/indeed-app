@extends('layouts.app')

@section('title', isset($student) ? 'Edit Student' : 'Create Student')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('students.index') }}" class="btn btn-link text-decoration-none me-3">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h4 class="fw-bold mb-0">
            {{ isset($student) ? 'Edit Student: ' . $student->full_name : 'Register New Student' }}
        </h4>
    </div>

    <div class="row">
        <div class="col-md-10 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    
                    <form method="POST" action="{{ isset($student) ? route('students.update', $student->id) : route('students.store') }}">
                        @csrf
                        @if(isset($student)) @method('PUT') @endif

                        <!-- STUDENT DETAILS -->
                        <h6 class="text-uppercase text-secondary fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">Student Details</h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Admission Number *</label>
                                <input type="text" name="admission_number" class="form-control @error('admission_number') is-invalid @enderror" value="{{ old('admission_number', $student->admission_number ?? '') }}" placeholder="e.g. ADM-001" required autofocus>
                                @error('admission_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date of Birth *</label>
                                <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth', isset($student->date_of_birth) ? $student->date_of_birth->format('Y-m-d') : '') }}" required>
                                @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name *</label>
                                <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $student->first_name ?? '') }}" placeholder="e.g. John" required>
                                @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name *</label>
                                <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $student->last_name ?? '') }}" placeholder="e.g. Doe" required>
                                @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Gender *</label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                <option value="">Select Gender</option>
                                <option value="MALE" {{ old('gender', $student->gender ?? '') == 'MALE' ? 'selected' : '' }}>Male</option>
                                <option value="FEMALE" {{ old('gender', $student->gender ?? '') == 'FEMALE' ? 'selected' : '' }}>Female</option>
                                <option value="OTHER" {{ old('gender', $student->gender ?? '') == 'OTHER' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <!-- In Student Form -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Transport Route</label>
                            <select name="transport_route_id" class="form-select @error('transport_route_id') is-invalid @enderror">
                                <option value="">No Transport</option>
                                @foreach($transportRoutes as $route)
                                    <option value="{{ $route->id }}" {{ old('transport_route_id', $student->transport_route_id ?? '') == $route->id ? 'selected' : '' }}>
                                        {{ $route->name }} (KES {{ number_format($route->amount, 2) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('transport_route_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <hr class="my-4 text-muted">

                        <!-- PARENT / GUARDIAN DETAILS -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-uppercase text-secondary fw-bold mb-0" style="font-size: 0.75rem; letter-spacing: 0.5px;">Parent / Guardian Details</h6>
                            <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">At least one required</span>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Mother's Name</label>
                                <input type="text" name="mother_name" class="form-control @error('mother_name') is-invalid @enderror" value="{{ old('mother_name', $student->mother_name ?? '') }}" placeholder="Mother's Full Name">
                                @error('mother_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Father's Name</label>
                                <input type="text" name="father_name" class="form-control @error('father_name') is-invalid @enderror" value="{{ old('father_name', $student->father_name ?? '') }}" placeholder="Father's Full Name">
                                @error('father_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Guardian's Name</label>
                                <input type="text" name="guardian_name" class="form-control @error('guardian_name') is-invalid @enderror" value="{{ old('guardian_name', $student->guardian_name ?? '') }}" placeholder="Guardian's Full Name">
                                @error('guardian_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="text-secondary" style="font-size: 0.7rem;">Required if no parents listed.</small>
                            </div>
                        </div>

                        <hr class="my-4 text-muted">

                        <!-- ACTIONS -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('students.index') }}" class="btn btn-light border">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i> {{ isset($student) ? 'Update Student' : 'Create Student' }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection