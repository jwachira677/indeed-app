@extends('layouts.app')

@section('title', 'Teachers List')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Teachers</h4>
            <p class="text-secondary mb-0">Manage all registered teachers</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 py-3 ps-3">Name</th>
                            <th class="border-0 py-3">Email</th>
                            <th class="border-0 py-3">Phone</th>
                            <th class="border-0 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teachers as $teacher)
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($teacher->name) }}&background=0D6EFD&color=fff&size=32" 
                                         alt="{{ $teacher->name }}" class="rounded-circle me-2" width="32" height="32">
                                    <div class="fw-semibold">{{ $teacher->name }}</div>
                                </div>
                            </td>
                            <td>{{ $teacher->email }}</td>
                            <td>{{ $teacher->phone_number ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $teacher->status === 'active' ? 'success' : 'secondary' }} rounded-pill">
                                    {{ ucfirst($teacher->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-secondary">
                                <i class="bi bi-person-workspace fs-1"></i>
                                <p class="mt-2 mb-0">No teachers found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($teachers->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $teachers->links() }}
        </div>
        @endif
    </div>
</div>
@endsection