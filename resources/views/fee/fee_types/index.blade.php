@extends('layouts.app')

@section('title', 'Fee Types Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Fee Types Management</h4>
            <p class="text-secondary mb-0">Manage all standard fees applied to students</p>
        </div>
        @can('fee-types.create')
        <a href="{{ route('fee-types.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i> Add Fee Type
        </a>
        @endcan
    </div>

    <!-- Fee Types Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 py-3 ps-3">Sno.</th>
                            <th class="border-0 py-3">Code</th>
                            <th class="border-0 py-3">Name</th>
                            <th class="border-0 py-3">Frequency</th>
                            <th class="border-0 py-3">Status</th>
                            <th class="border-0 py-3">Description</th>
                            <th class="border-0 py-3 text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($feeTypes as $index => $feeType)
                            <tr>
                                <td class="ps-3">{{ $feeTypes->firstItem() + $index }}</td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">
                                        {{ $feeType->code }}
                                    </span>
                                </td>
                                <td class="fw-semibold">{{ $feeType->name }}</td>
                                <td>
                                    <span class="text-secondary" style="font-size: 0.85rem;">
                                        {{ \App\Models\FeeType::getBillingFrequencies()[$feeType->billing_frequency] ?? $feeType->billing_frequency }}
                                    </span>
                                </td>
                                <td>
                                    @if($feeType->is_active)
                                        <span class="badge bg-success rounded-pill">Active</span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-secondary" style="font-size: 0.85rem;">
                                        {{ \Illuminate\Support\Str::limit($feeType->description, 50) ?: '—' }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group">
                                        @can('fee-types.edit')
                                        <a href="{{ route('fee-types.edit', $feeType->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('fee-types.delete')
                                        <form action="{{ route('fee-types.destroy', $feeType->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this fee type?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-tags fs-1 text-secondary"></i>
                                    <p class="text-secondary mt-2 mb-3">No fee types found</p>
                                    @can('fee-types.create')
                                    <a href="{{ route('fee-types.create') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-circle me-2"></i> Add First Fee Type
                                    </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        @if($feeTypes->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="text-secondary" style="font-size: 0.85rem;">
                    Showing {{ $feeTypes->firstItem() ?? 0 }} to {{ $feeTypes->lastItem() ?? 0 }} of {{ $feeTypes->total() ?? 0 }} fee types
                </span>
                {{ $feeTypes->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection