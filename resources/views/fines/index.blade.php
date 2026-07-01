@extends('layouts.app')

@section('title', 'Fines Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Fines Management</h4>
            <p class="text-secondary mb-0">Manage member fines and penalties</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('fines.export') }}" class="btn btn-outline-success">
                <i class="bi bi-download me-2"></i> Export
            </a>
            <a href="{{ route('fines.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i> Issue Fine
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Total Fines</p>
                            <h4 class="fw-bold mb-0 text-danger">KES {{ number_format($summary['total_fines'] ?? 0, 2) }}</h4>
                        </div>
                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-3">
                            <i class="bi bi-exclamation-triangle fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Total Paid</p>
                            <h4 class="fw-bold mb-0 text-success">KES {{ number_format($summary['total_paid'] ?? 0, 2) }}</h4>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-3">
                            <i class="bi bi-check-circle fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Total Unpaid</p>
                            <h4 class="fw-bold mb-0 text-warning">KES {{ number_format($summary['total_unpaid'] ?? 0, 2) }}</h4>
                        </div>
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                            <i class="bi bi-clock fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Overdue</p>
                            <h4 class="fw-bold mb-0 text-danger">KES {{ number_format($summary['total_overdue'] ?? 0, 2) }}</h4>
                        </div>
                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-3">
                            <i class="bi bi-clock-history fs-4"></i>
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
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Search by member or fine #..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        <option value="waived" {{ request('status') == 'waived' ? 'selected' : '' }}>Waived</option>
                        <option value="partially_paid" {{ request('status') == 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">From Date</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To Date</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Fines Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 py-3 ps-3">Fine #</th>
                            <th class="border-0 py-3">Member</th>
                            <th class="border-0 py-3">Amount</th>
                            <th class="border-0 py-3">Paid</th>
                            <th class="border-0 py-3">Balance</th>
                            <th class="border-0 py-3">Reason</th>
                            <th class="border-0 py-3">Status</th>
                            <th class="border-0 py-3">Due Date</th>
                            <th class="border-0 py-3 text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fines as $fine)
                        <tr>
                            <td class="ps-3">
                                <span class="fw-bold text-primary">{{ $fine->fine_number }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="fw-semibold" style="font-size: 0.9rem;">
                                            {{ $fine->user->name ?? 'N/A' }}
                                        </div>
                                        <span class="text-secondary" style="font-size: 0.7rem;">
                                            {{ $fine->user->member_id ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="fw-bold text-danger">
                                KES {{ number_format($fine->amount, 2) }}
                            </td>
                            <td class="text-success">
                                KES {{ number_format($fine->amount_paid, 2) }}
                            </td>
                            <td class="fw-bold {{ $fine->balance > 0 ? 'text-warning' : 'text-success' }}">
                                KES {{ number_format($fine->balance, 2) }}
                            </td>
                            <td>
                                <span style="font-size: 0.85rem;">{{ Str::limit($fine->reason, 30) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $fine->status_badge }} rounded-pill">
                                    {{ ucfirst(str_replace('_', ' ', $fine->status)) }}
                                </span>
                                @if($fine->is_overdue)
                                    <span class="badge bg-danger ms-1">
                                        {{ $fine->days_overdue }} days overdue
                                    </span>
                                @endif
                            </td>
                            <td style="font-size: 0.85rem;">
                                {{ $fine->due_date ? $fine->due_date->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group">
                                    <a href="{{ route('fines.show', $fine) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(!in_array($fine->status, ['paid', 'waived']))
                                        <a href="{{ route('fines.edit', $fine) }}" 
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="bi bi-exclamation-triangle fs-1 text-secondary"></i>
                                <p class="text-secondary mt-2">No fines found</p>
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
                    Showing {{ $fines->firstItem() ?? 0 }} to {{ $fines->lastItem() ?? 0 }} of {{ $fines->total() ?? 0 }} entries
                </span>
                {{ $fines->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection