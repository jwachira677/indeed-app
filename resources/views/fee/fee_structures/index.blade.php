@extends('layouts.app')

@section('title', 'Fee Structures')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Grade Level Fee Structures</h4>
            <p class="text-secondary mb-0">Select a grade level to view and manage its fees</p>
        </div>
        @if(request('grade_level_id'))
            @can('fee-structures.create')
            <a href="{{ route('fee-structures.create', ['grade_level_id' => request('grade_level_id')]) }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i> Add Fee to this Grade
            </a>
            @endcan
        @endif
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('fee-structures.index') }}" class="row g-3 align-items-end" id="filterForm">
                <div class="col-md-4">
                    <label class="form-label fw-bold text-primary">Select Grade Level *</label>
                    <select name="grade_level_id" class="form-select border-primary" required>
                        <option value="">-- Choose a Grade Level --</option>
                        @foreach($gradeLevels as $grade)
                            <option value="{{ $grade->id }}" {{ request('grade_level_id') == $grade->id ? 'selected' : '' }}>
                                {{ $grade->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Term (Optional)</label>
                    <select name="term_id" class="form-select">
                        <option value="">All Terms</option>
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}" {{ request('term_id') == $term->id ? 'selected' : '' }}>
                                Term {{ $term->term_number }} ({{ $term->academicYear->year_name ?? '' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fee Type (Optional)</label>
                    <select name="fee_type_id" class="form-select">
                        <option value="">All Fee Types</option>
                        @foreach($feeTypes as $type)
                            <option value="{{ $type->id }}" {{ request('fee_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-search me-1"></i> Load
                    </button>
                    <a href="{{ route('fee-structures.index') }}" class="btn btn-outline-secondary" title="Reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Conditional Display -->
    @if(!request('grade_level_id'))
        
        <!-- Empty State (No Grade Selected) -->
        <div class="card border-0 shadow-sm py-5 text-center">
            <div class="card-body">
                <i class="bi bi-journal-text fs-1 text-secondary mb-3 d-block"></i>
                <h5 class="fw-bold">No Grade Level Selected</h5>
                <p class="text-secondary mb-0">Please choose a Grade Level from the dropdown above to load its fee structure.</p>
            </div>
        </div>

    @else
        <!-- Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="fw-bold mb-0">
                    Fee Structure for: 
                    <span class="text-primary">{{ $gradeLevels->firstWhere('id', request('grade_level_id'))->name ?? 'Unknown Grade' }}</span>
                </h5>
            </div>
            <div class="card-body p-0 mt-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 py-3 ps-4">Sno.</th>
                                <th class="border-0 py-3">Term</th>
                                <th class="border-0 py-3">Fee Type</th>
                                <th class="border-0 py-3">Amount (KES)</th>
                                <th class="border-0 py-3">Due Date</th>
                                <th class="border-0 py-3 text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($feeStructures as $index => $structure)
                                <tr>
                                    <td class="ps-4">{{ $feeStructures->firstItem() + $index }}</td>
                                    <td>
                                        <span class="badge bg-secondary rounded-pill">
                                            Term {{ $structure->term->term_number ?? 'N/A' }} ({{ $structure->term->academicYear->year_name ?? '' }})
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-semibold">{{ $structure->feeType->name ?? 'N/A' }}</span>
                                        @if($structure->is_optional)
                                            <span class="badge bg-warning text-dark ms-2">Optional</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold text-success">
                                        {{ number_format($structure->amount, 2) }}
                                    </td>
                                    <td>
                                        {{ $structure->due_date ? $structure->due_date->format('d M, Y') : '—' }}
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            @can('fee-structures.edit')
                                            <a href="{{ route('fee-structures.edit', $structure->id) }}" class="btn btn-sm btn-outline-primary" title="Edit Fee">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @endcan
                                            
                                            @can('fee-structures.delete')
                                            <form action="{{ route('fee-structures.destroy', $structure->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this fee? It will be removed for all streams in this grade level.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Fee">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-secondary"></i>
                                        <p class="text-secondary mt-2 mb-3">No fees have been set up for this grade level yet.</p>
                                        @can('fee-structures.create')
                                        <a href="{{ route('fee-structures.create', ['grade_level_id' => request('grade_level_id')]) }}" class="btn btn-primary btn-sm">
                                            <i class="bi bi-plus-circle me-1"></i> Add First Fee
                                        </a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if($feeStructures->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    {{ $feeStructures->appends(request()->query())->links() }}
                </div>
            </div>
            @endif
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Auto-submit form when the Grade Level dropdown changes
    document.querySelector('select[name="grade_level_id"]').addEventListener('change', function() {
        if(this.value !== '') {
            document.getElementById('filterForm').submit();
        }
    });
</script>
@endpush