@extends('layouts.app')

@section('title', 'Transport Routes')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Transport Routes</h4>
            <p class="text-secondary mb-0">Manage routes, zones, and their fees</p>
        </div>
        <a href="{{ route('transport-routes.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i> Add Route
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 ps-3">Sno.</th>
                            <th class="py-3">Route Name</th>
                            <th class="py-3">Amount (KES)</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($routes as $index => $route)
                            <tr>
                                <td class="ps-3">{{ $routes->firstItem() + $index }}</td>
                                <td class="fw-semibold">{{ $route->name }}</td>
                                <td class="fw-bold text-success">{{ number_format($route->amount, 2) }}</td>
                                <td>
                                    @if($route->is_active)
                                        <span class="badge bg-success rounded-pill">Active</span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group">
                                        <a href="{{ route('transport-routes.edit', $route->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                        <form action="{{ route('transport-routes.destroy', $route->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this route?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-5 text-secondary">No transport routes found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($routes->hasPages())
        <div class="card-footer bg-white border-0 py-3">{{ $routes->links() }}</div>
        @endif
    </div>
</div>
@endsection