@extends('layouts.app')

@section('title', isset($user) ? 'Edit User' : 'Create User')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('users.index') }}" class="btn btn-link text-decoration-none me-3">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h4 class="fw-bold mb-0">{{ isset($user) ? 'Edit User' : 'Create New User' }}</h4>
    </div>

    <div class="row">
        <!-- Main Form Card -->
        <div class="col-xl-7 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST" 
                          action="{{ isset($user) ? route('users.update', $user) : route('users.store') }}" 
                          enctype="multipart/form-data">
                        @csrf
                        @if(isset($user))
                            @method('PUT')
                        @endif

                        <!-- Personal Information -->
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="bi bi-person-badge me-2"></i> Personal Information
                        </h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $user->name ?? '') }}" 
                                       placeholder="Enter full name"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $user->email ?? '') }}" 
                                       placeholder="Enter email address"
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="tel" 
                                       class="form-control @error('phone_number') is-invalid @enderror" 
                                       id="phone_number" 
                                       name="phone_number" 
                                       value="{{ old('phone_number', $user->phone_number ?? '') }}" 
                                       placeholder="Enter phone number">
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="national_id" class="form-label">National ID</label>
                                <input type="text" 
                                       class="form-control @error('national_id') is-invalid @enderror" 
                                       id="national_id" 
                                       name="national_id" 
                                       value="{{ old('national_id', $user->national_id ?? '') }}" 
                                       placeholder="Enter national ID">
                                @error('national_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="date_of_birth" class="form-label">Date of Birth</label>
                                <input type="date" 
                                       class="form-control @error('date_of_birth') is-invalid @enderror" 
                                       id="date_of_birth" 
                                       name="date_of_birth" 
                                       value="{{ old('date_of_birth', isset($user) && $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '') }}">
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" 
                                          name="address" 
                                          rows="1" 
                                          placeholder="Enter address">{{ old('address', $user->address ?? '') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>

                        <!-- Account Information -->
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="bi bi-shield-lock me-2"></i> Account Information
                        </h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="role_id" class="form-label">Role *</label>
                                <select name="role_id" 
                                        class="form-select @error('role_id') is-invalid @enderror" 
                                        required>
                                    <option value="">Select Role</option>
                                    @foreach($roles ?? [] as $role)
                                        <option value="{{ $role->id }}" 
                                            {{ old('role_id', $user->role_id ?? '') == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status *</label>
                                <select name="status" 
                                        class="form-select @error('status') is-invalid @enderror" 
                                        required>
                                    <option value="">Select Status</option>
                                    <option value="active" {{ old('status', $user->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="pending" {{ old('status', $user->status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="suspended" {{ old('status', $user->status ?? '') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    <option value="inactive" {{ old('status', $user->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">
                                    {{ isset($user) ? 'New Password (leave blank to keep current)' : 'Password *' }}
                                </label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       placeholder="{{ isset($user) ? 'Enter new password' : 'Enter password' }}"
                                       {{ isset($user) ? '' : 'required' }}>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if(isset($user))
                                    <small class="text-secondary">Leave blank to keep current password</small>
                                @endif
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       placeholder="Confirm password">
                            </div>
                        </div>

                        <hr>

                        <!-- Member Information (shown only for members) -->
                        <div id="memberFields" style="display: {{ old('role_id', $user->role_id ?? '') == 2 ? 'block' : 'none' }};">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="bi bi-person-vcard me-2"></i> Member Information
                            </h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="member_id" class="form-label">Member ID</label>
                                    <input type="text" 
                                           class="form-control @error('member_id') is-invalid @enderror" 
                                           id="member_id" 
                                           name="member_id" 
                                           value="{{ old('member_id', $user->member_id ?? '') }}" 
                                           placeholder="Auto-generated if left blank"
                                           {{ isset($user) && $user->member_id ? 'readonly' : '' }}>
                                    @error('member_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-secondary">Leave blank to auto-generate</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="joined_at" class="form-label">Joined Date</label>
                                    <input type="date" 
                                           class="form-control @error('joined_at') is-invalid @enderror" 
                                           id="joined_at" 
                                           name="joined_at" 
                                           value="{{ old('joined_at', isset($user) && $user->joined_at ? $user->joined_at->format('Y-m-d') : date('Y-m-d')) }}">
                                    @error('joined_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i> 
                                {{ isset($user) ? 'Update User' : 'Create User' }}
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    // Show/hide member fields based on role selection
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role_id');
        const memberFields = document.getElementById('memberFields');
        
        if (roleSelect) {
            roleSelect.addEventListener('change', function() {
                // Check if selected role is 'member' (role_id = 2)
                if (this.value == '2') {
                    memberFields.style.display = 'block';
                } else {
                    memberFields.style.display = 'none';
                }
            });
        }
    });
</script>
@endpush
@endsection