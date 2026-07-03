@extends('layouts.app')

@section('title', 'Loan Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Loan Management</h4>
            <p class="text-secondary mb-0">Manage all loan applications</p>
        </div>
        <a href="{{ route('loans.eligibility.form') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i> New Loan Application
        </a>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Search by loan # or member..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="defaulted" {{ request('status') == 'defaulted' ? 'selected' : '' }}>Defaulted</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
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

    <!-- Loans Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 py-3 ps-3">Loan #</th>
                            <th class="border-0 py-3">Member</th>
                            <th class="border-0 py-3">Amount</th>
                            <th class="border-0 py-3">Type</th>
                            <th class="border-0 py-3">Due Date</th>
                            <th class="border-0 py-3">Status</th>
                            <th class="border-0 py-3">Date Applied</th>
                            <th class="border-0 py-3 text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans ?? [] as $loan)
                        <tr>
                            <td class="ps-3">
                                <span class="fw-bold text-primary">{{ $loan->loan_number }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="fw-semibold" style="font-size: 0.9rem;">
                                            {{ $loan->user->name ?? 'N/A' }}
                                        </div>
                                        <span class="text-secondary" style="font-size: 0.7rem;">
                                            {{ $loan->user->member_id ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="fw-bold">
                                KES {{ number_format($loan->amount_taken, 2) }}
                            </td>
                            <td>
                                <span class="badge bg-{{ $loan->loan_type == 'instant' ? 'warning' : 'info' }}">
                                    {{ ucfirst($loan->loan_type) }}
                                </span>
                            </td>
                            <td style="font-size: 0.85rem;">
                                {{ $loan->date_due->format('M d, Y') }}
                                @if($loan->isOverdue())
                                    <span class="badge bg-danger ms-1">Overdue</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $loan->status_badge }} rounded-pill">
                                    {{ ucfirst($loan->status) }}
                                </span>
                            </td>
                            <td style="font-size: 0.85rem;">
                                {{ $loan->created_at->format('M d, Y') }}
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route('loans.show', $loan) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-file-earmark-text fs-1 text-secondary"></i>
                                <p class="text-secondary mt-2">No loan applications found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(isset($loans) && method_exists($loans, 'links'))
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-secondary" style="font-size: 0.85rem;">
                    Showing {{ $loans->firstItem() ?? 0 }} to {{ $loans->lastItem() ?? 0 }} of {{ $loans->total() ?? 0 }} entries
                </span>
                {{ $loans->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection