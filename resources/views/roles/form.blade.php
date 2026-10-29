@extends('layouts.app')

@section('title', isset($role) ? 'Edit Role' : 'Create Role')

@section('content')
<div class="container-fluid">
    
    <!-- Header / Back Button -->
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('roles.index') }}" class="btn btn-link text-decoration-none me-3">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h4 class="fw-bold mb-0">{{ isset($role) ? 'Edit Role: ' . $role->name : 'Create New Role' }}</h4>
    </div>

    <div class="row">
        <!-- Form inside a Card -->
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    
                    <form method="POST" action="{{ isset($role) ? route('roles.update', $role) : route('roles.store') }}">
                        @csrf
                        @if(isset($role))
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label for="name" class="form-label">Role Name *</label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $role->name ?? '') }}" 
                                   placeholder="e.g., Administrator"
                                   required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug *</label>
                            <input type="text" 
                                   class="form-control @error('slug') is-invalid @enderror" 
                                   id="slug" 
                                   name="slug" 
                                   value="{{ old('slug', $role->slug ?? '') }}" 
                                   placeholder="e.g., admin"
                                   required>
                            <small class="text-secondary">Unique identifier (lowercase, hyphens instead of spaces).</small>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3"
                                      placeholder="Brief description of what this role can do...">{{ old('description', $role->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input @error('is_default') is-invalid @enderror" 
                                   id="is_default" 
                                   name="is_default" 
                                   value="1" 
                                   {{ old('is_default', $role->is_default ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_default">
                                Set as default role for new users
                            </label>
                            @error('is_default')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4 text-muted">

                        <!-- Actions: Submit and Cancel -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('roles.index') }}" class="btn btn-light border">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i> {{ isset($role) ? 'Update Role' : 'Create Role' }}
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-generate slug from name
    document.getElementById('name').addEventListener('input', function() {
        const slugInput = document.getElementById('slug');
        if (!slugInput.dataset.touched) {
            slugInput.value = this.value
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/(^-|-$)+/g, '');
        }
    });
    document.getElementById('slug').addEventListener('input', function() {
        this.dataset.touched = true;
    });
</script>
@endpush