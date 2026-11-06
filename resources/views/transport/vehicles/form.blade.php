@extends('layouts.app')

@section('title', isset($vehicle) ? 'Edit Vehicle' : 'Create Vehicle')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('vehicles.index') }}" class="btn btn-link text-decoration-none me-3">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h4 class="fw-bold mb-0">{{ isset($vehicle) ? 'Edit Vehicle: ' . $vehicle->registration_number : 'Register New Vehicle' }}</h4>
    </div>

    <div class="row">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    
                    <form method="POST" action="{{ isset($vehicle) ? route('vehicles.update', $vehicle->id) : route('vehicles.store') }}">
                        @csrf
                        @if(isset($vehicle)) @method('PUT') @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Registration Number *</label>
                                <input type="text" name="registration_number" class="form-control text-uppercase @error('registration_number') is-invalid @enderror" value="{{ old('registration_number', $vehicle->registration_number ?? '') }}" placeholder="e.g. KCA 123X" required autofocus>
                                @error('registration_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Model / Make *</label>
                                <input type="text" name="model" class="form-control @error('model') is-invalid @enderror" value="{{ old('model', $vehicle->model ?? '') }}" placeholder="e.g. Toyota Coaster" required>
                                @error('model') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Seating Capacity *</label>
                                <input type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror" value="{{ old('capacity', $vehicle->capacity ?? '') }}" placeholder="e.g. 33" min="1" required>
                                @error('capacity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status *</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="ACTIVE" {{ old('status', $vehicle->status ?? 'ACTIVE') == 'ACTIVE' ? 'selected' : '' }}>Active</option>
                                    <option value="MAINTENANCE" {{ old('status', $vehicle->status ?? '') == 'MAINTENANCE' ? 'selected' : '' }}>Under Maintenance</option>
                                    <option value="INACTIVE" {{ old('status', $vehicle->status ?? '') == 'INACTIVE' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Assign Driver</label>
                            <select name="driver_id" class="form-select @error('driver_id') is-invalid @enderror">
                                <option value="">-- No Driver Assigned --</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}" {{ old('driver_id', $vehicle->driver_id ?? '') == $driver->id ? 'selected' : '' }}>
                                        {{ $driver->name }} ({{ $driver->email }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-secondary">Select from registered users.</small>
                            @error('driver_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('vehicles.index') }}" class="btn btn-light border">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i> {{ isset($vehicle) ? 'Update Vehicle' : 'Save Vehicle' }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection