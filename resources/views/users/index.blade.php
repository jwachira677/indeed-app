@extends('layouts.app')

@section('title', 'Users Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Users Management</h4>
            <p class="text-secondary mb-0">Manage all registered users and members</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-2"></i> Add User
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm hover-shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Total Users</p>
                            <h4 class="fw-bold mb-0 text-primary">{{ $users->count() }}</h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                            <i class="bi bi-people fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm hover-shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Active</p>
                            <h4 class="fw-bold mb-0 text-success">{{ $users->where('status', 'active')->count() }}</h4>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-3">
                            <i class="bi bi-check-circle fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm hover-shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Pending</p>
                            <h4 class="fw-bold mb-0 text-warning">{{ $users->where('status', 'pending')->count() }}</h4>
                        </div>
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                            <i class="bi bi-hourglass-split fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm hover-shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Suspended</p>
                            <h4 class="fw-bold mb-0 text-danger">{{ $users->where('status', 'suspended')->count() }}</h4>
                        </div>
                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-3">
                            <i class="bi bi-exclamation-triangle fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               class="form-control border-start-0" 
                               placeholder="Search by name, email, ID..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="member" {{ request('role') == 'member' ? 'selected' : '' }}>Member</option>
                        <option value="guest" {{ request('role') == 'guest' ? 'selected' : '' }}>Guest</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sort By</label>
                    <select name="sort" class="form-select">
                        <option value="created_at_desc" {{ request('sort') == 'created_at_desc' ? 'selected' : '' }}>Newest First</option>
                        <option value="created_at_asc" {{ request('sort') == 'created_at_asc' ? 'selected' : '' }}>Oldest First</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 py-3 ps-3">Sno.</th>
                            <th class="border-0 py-3">Name</th>
                            <th class="border-0 py-3">Email</th>
                            <th class="border-0 py-3">Phone</th>
                            <th class="border-0 py-3">National ID</th>
                            <th class="border-0 py-3">Role</th>
                            <th class="border-0 py-3">Status</th>
                            <th class="border-0 py-3">Joined</th>
                            <th class="border-0 py-3 text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                        <tr>
                            <td class="ps-3">{{ $users->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0D6EFD&color=fff&size=32" 
                                         alt="{{ $user->name }}" 
                                         class="rounded-circle me-2" 
                                         width="32" 
                                         height="32">
                                    <div>
                                        <div class="fw-semibold" style="font-size: 0.9rem;">{{ $user->name }}</div>
                                        <span class="text-secondary" style="font-size: 0.7rem;">ID: {{ $user->id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-size: 0.85rem;">{{ $user->email }}</span>
                                @if($user->email_verified_at)
                                    <i class="bi bi-check-circle-fill text-success ms-1" title="Verified"></i>
                                @endif
                            </td>
                            <td>
                                <span style="font-size: 0.85rem;">{{ $user->phone_number ?? '-' }}</span>
                            </td>
                            <td>
                                <span style="font-size: 0.85rem;">{{ $user->national_id ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $user->role?->slug === 'admin' ? 'danger' : ($user->role?->slug === 'member' ? 'primary' : 'secondary') }} rounded-pill">
                                    {{ $user->role?->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $user->status === 'active' ? 'success' : ($user->status === 'pending' ? 'warning' : ($user->status === 'suspended' ? 'danger' : 'secondary')) }} rounded-pill">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td style="font-size: 0.8rem;">
                                @if($user->joined_at)
                                    {{ $user->joined_at->format('M d, Y') }}
                                    <br>
                                    <span class="text-secondary" style="font-size: 0.65rem;">
                                        {{ $user->joined_at->diffForHumans() }}
                                    </span>
                                @else
                                    {{ $user->created_at->format('M d, Y') }}
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group">
                                    <a href="{{ route('users.show', $user) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}" 
                                       class="btn btn-sm btn-outline-secondary" 
                                       title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($user->status !== 'active')
                                    <form action="{{ route('users.activate', $user) }}" 
                                          method="POST" 
                                          class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-success"
                                                title="Activate"
                                                onclick="return confirm('Activate this user?')">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @if($user->status !== 'suspended' && $user->status !== 'inactive')
                                    <form action="{{ route('users.suspend', $user) }}" 
                                          method="POST" 
                                          class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger"
                                                title="Suspend"
                                                onclick="return confirm('Suspend this user?')">
                                            <i class="bi bi-slash-circle"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <i class="bi bi-people fs-1 text-secondary"></i>
                                <p class="text-secondary mt-2">No users found</p>
                                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-person-plus me-2"></i> Add First User
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-secondary" style="font-size: 0.85rem;">
                    Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() ?? 0 }} users
                </span>
                {{ $users->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .hover-shadow {
        transition: all 0.3s ease;
    }
    .hover-shadow:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.08) !important;
    }
    
    .table > :not(caption) > * > * {
        padding: 0.8rem 0.5rem;
    }
    
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto-submit form on filter change
    document.querySelectorAll('select[name="status"], select[name="role"], select[name="sort"]').forEach(function(select) {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
</script>
@endpush
@endsection