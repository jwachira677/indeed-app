@extends('layouts.app')

@section('title', isset($transportRoute) ? 'Edit Transport Route' : 'Create Transport Route')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('transport-routes.index') }}" class="btn btn-link text-decoration-none me-3">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h4 class="fw-bold mb-0">{{ isset($transportRoute) ? 'Edit Route: ' . $transportRoute->name : 'Create Transport Route' }}</h4>
    </div>

    <div class="row">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    
                    <form method="POST" action="{{ isset($transportRoute) ? route('transport-routes.update', $transportRoute->id) : route('transport-routes.store') }}">
                        @csrf
                        @if(isset($transportRoute)) @method('PUT') @endif

                        <div class="mb-3">
                            <label class="form-label">Route Name *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $transportRoute->name ?? '') }}" placeholder="e.g. Route A - Westlands" required autofocus>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Amount (Per Term) *</label>
                            <div class="input-group">
                                <span class="input-group-text">KES</span>
                                <input type="number" step="0.01" min="0" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $transportRoute->amount ?? '') }}" placeholder="0.00" required>
                                @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-4 form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $transportRoute->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Is Active? (Available for assignment)</label>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('transport-routes.index') }}" class="btn btn-light border">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i> {{ isset($transportRoute) ? 'Update Route' : 'Create Route' }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection