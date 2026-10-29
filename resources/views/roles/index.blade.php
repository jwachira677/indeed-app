@extends('layouts.app')

@section('title', 'Roles Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Role Management</h4>
            <p class="text-secondary mb-0">Manage the roles available to users in your application</p>
        </div>
        <a href="{{ route('roles.create') }}" class="btn btn-primary">
            <i class="bi bi-shield-plus me-2"></i> Add Role
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm hover-shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Total Roles</p>
                            <h4 class="fw-bold mb-0 text-primary">{{ $roles->total() }}</h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                            <i class="bi bi-shield-lock fs-4"></i>
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
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Default Roles</p>
                            <h4 class="fw-bold mb-0 text-success">{{ $roles->where('is_default', true)->count() }}</h4>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-3">
                            <i class="bi bi-star fs-4"></i>
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
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Custom Roles</p>
                            <h4 class="fw-bold mb-0 text-warning">{{ $roles->where('is_default', false)->count() }}</h4>
                        </div>
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                            <i class="bi bi-gear fs-4"></i>
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
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Created This Month</p>
                            <h4 class="fw-bold mb-0 text-info">{{ $roles->where('created_at', '>=', now()->startOfMonth())->count() }}</h4>
                        </div>
                        <div class="bg-info bg-opacity-10 text-info rounded-circle p-3">
                            <i class="bi bi-calendar-plus fs-4"></i>
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
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text"
                               name="search"
                               class="form-control border-start-0"
                               placeholder="Search by name, slug or description..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="default" {{ request('type') === 'default' ? 'selected' : '' }}>Default</option>
                        <option value="custom" {{ request('type') === 'custom' ? 'selected' : '' }}>Custom</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sort By</label>
                    <select name="sort" class="form-select">
                        <option value="created_at_desc" {{ request('sort') === 'created_at_desc' ? 'selected' : '' }}>Newest First</option>
                        <option value="created_at_asc" {{ request('sort') === 'created_at_asc' ? 'selected' : '' }}>Oldest First</option>
                        <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                        <option value="slug_asc" {{ request('sort') === 'slug_asc' ? 'selected' : '' }}>Slug (A-Z)</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary" title="Reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Roles Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 py-3 ps-3">Sno.</th>
                            <th class="border-0 py-3">Role</th>
                            <th class="border-0 py-3">Slug</th>
                            <th class="border-0 py-3">Description</th>
                            <th class="border-0 py-3">Type</th>
                            <th class="border-0 py-3">Created</th>
                            <th class="border-0 py-3 text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $index => $role)
                        <tr>
                            <td class="ps-3">{{ $roles->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-2
                                                {{ $role->is_default ? 'bg-primary bg-opacity-10 text-primary' : 'bg-secondary bg-opacity-10 text-secondary' }}"
                                         style="width: 36px; height: 36px;">
                                        <i class="bi bi-shield-check"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold" style="font-size: 0.9rem;">{{ $role->name }}</div>
                                        <span class="text-secondary" style="font-size: 0.7rem;">ID: {{ $role->id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <code class="text-secondary" style="font-size: 0.8rem;">{{ $role->slug }}</code>
                            </td>
                            <td>
                                <span class="text-secondary" style="font-size: 0.85rem;">
                                    {{ \Illuminate\Support\Str::limit($role->description, 60) ?: '—' }}
                                </span>
                            </td>
                            <td>
                                @if($role->is_default)
                                    <span class="badge bg-primary rounded-pill">Default</span>
                                @else
                                    <span class="badge bg-secondary rounded-pill">Custom</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-secondary" style="font-size: 0.8rem;">
                                    {{ optional($role->created_at)->format('d M, Y') ?? '—' }}
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group">
                                    <a href="{{ route('roles.show', $role) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('roles.edit', $role) }}"
                                       class="btn btn-sm btn-outline-secondary"
                                       title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @unless($role->is_default)
                                        <form action="{{ route('roles.destroy', $role) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Delete the role \'{{ $role->name }}\'? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-shield-slash fs-1 text-secondary"></i>
                                <p class="text-secondary mt-2 mb-3">No roles found</p>
                                <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-shield-plus me-2"></i> Add First Role
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="text-secondary" style="font-size: 0.85rem;">
                    Showing {{ $roles->firstItem() ?? 0 }} to {{ $roles->lastItem() ?? 0 }} of {{ $roles->total() ?? 0 }} roles
                </span>
                {{ $roles->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .hover-shadow { transition: all 0.3s ease; }
    .hover-shadow:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.08) !important;
    }
    .table > :not(caption) > * > * { padding: 0.8rem 0.5rem; }
    .btn-group .btn { padding: 0.25rem 0.5rem; }
</style>
@endpush

@push('scripts')
<script>
    document.querySelectorAll('select[name="type"], select[name="sort"]').forEach(function (select) {
        select.addEventListener('change', function () {
            this.closest('form').submit();
        });
    });
</script>
@endpush