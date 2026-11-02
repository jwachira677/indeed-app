@extends('layouts.app')

@section('title', isset($feeType) ? 'Edit Fee Type' : 'Create Fee Type')

@section('content')
<div class="container-fluid">
    <!-- Header / Back Button -->
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('fee-types.index') }}" class="btn btn-link text-decoration-none me-3">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h4 class="fw-bold mb-0">{{ isset($feeType) ? 'Edit Fee Type: ' . $feeType->name : 'Create New Fee Type' }}</h4>
    </div>

    <div class="row">
        <!-- Form inside a Card -->
        <div class="col-md-10 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    
                    <form method="POST" action="{{ isset($feeType) ? route('fee-types.update', $feeType->id) : route('fee-types.store') }}">
                        @csrf
                        @if(isset($feeType))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <!-- Code -->
                            <div class="col-md-4 mb-3">
                                <label for="code" class="form-label">Fee Code *</label>
                                <input type="text" 
                                       class="form-control text-uppercase @error('code') is-invalid @enderror" 
                                       id="code" 
                                       name="code" 
                                       value="{{ old('code', $feeType->code ?? '') }}" 
                                       placeholder="e.g., TUITION"
                                       required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-secondary">Unique identifier (e.g., TUITION, TRANSPORT).</small>
                            </div>

                            <!-- Name -->
                            <div class="col-md-8 mb-3">
                                <label for="name" class="form-label">Fee Name *</label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $feeType->name ?? '') }}" 
                                       placeholder="e.g., Tuition Fee"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Billing Frequency -->
                            <div class="col-md-6 mb-3">
                                <label for="billing_frequency" class="form-label">Billing Frequency *</label>
                                <select name="billing_frequency" 
                                        class="form-select @error('billing_frequency') is-invalid @enderror" 
                                        required>
                                    <option value="">Select Frequency</option>
                                    @foreach(\App\Models\FeeType::getBillingFrequencies() as $value => $label)
                                        <option value="{{ $value }}" {{ old('billing_frequency', $feeType->billing_frequency ?? '') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('billing_frequency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3"
                                      placeholder="Brief description of what this fee covers...">{{ old('description', $feeType->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4 text-muted">

                        <!-- Toggles -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="is_optional" name="is_optional" value="1" 
                                           {{ old('is_optional', $feeType->is_optional ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_optional">Is Optional? (Parents can opt out)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" 
                                           {{ old('is_active', $feeType->is_active ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Is Active? (Available for billing)</label>
                                </div>
                            </div>
                        </div>

                        <!-- Actions: Submit and Cancel -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('fee-types.index') }}" class="btn btn-light border">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i> {{ isset($feeType) ? 'Update Fee Type' : 'Create Fee Type' }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection