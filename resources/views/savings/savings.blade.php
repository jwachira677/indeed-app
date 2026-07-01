@extends('layouts.app')

@section('title', 'Savings')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Savings Management</h4>
            <p class="text-secondary mb-0">
                Financial Year: <strong>{{ $financialYear }}</strong>
                @if($selectedMonth)
                    | Month: <strong>{{ date('F', mktime(0, 0, 0, $selectedMonth, 1)) }}</strong>
                @endif
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('savings.export') }}?financial_year={{ $financialYear }}" 
               class="btn btn-outline-success">
                <i class="bi bi-download me-2"></i> Export
            </a>
            <a href="{{ route('savings.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i> Record Savings
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm hover-shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Total Savings</p>
                            <h4 class="fw-bold mb-0 text-primary">KES {{ number_format($summary['total_savings'], 2) }}</h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                            <i class="bi bi-piggy-bank fs-4"></i>
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
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Total Entries</p>
                            <h4 class="fw-bold mb-0 text-success">{{ number_format($summary['total_entries']) }}</h4>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-3">
                            <i class="bi bi-list-check fs-4"></i>
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
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Active Members</p>
                            <h4 class="fw-bold mb-0 text-info">{{ number_format($summary['total_members']) }}</h4>
                        </div>
                        <div class="bg-info bg-opacity-10 text-info rounded-circle p-3">
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
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Pending Approvals</p>
                            <h4 class="fw-bold mb-0 text-warning">{{ number_format($summary['pending_count']) }}</h4>
                        </div>
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                            <i class="bi bi-hourglass-split fs-4"></i>
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
                    <label class="form-label">Financial Year</label>
                    <select name="financial_year" class="form-select">
                        @foreach($financialYears as $year)
                            <option value="{{ $year }}" {{ $financialYear == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-select">
                        <option value="">All Months</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Search by member..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Savings Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 py-3 ps-3">Receipt #</th>
                            <th class="border-0 py-3">Member</th>
                            <th class="border-0 py-3">Amount</th>
                            <th class="border-0 py-3">Month/Year</th>
                            <th class="border-0 py-3">Payment Method</th>
                            <th class="border-0 py-3">Status</th>
                            <th class="border-0 py-3">Date</th>
                            <th class="border-0 py-3 text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($savings as $saving)
                        <tr>
                            <td class="ps-3">
                                <span class="fw-bold text-primary">{{ $saving->receipt_number }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $saving->user->avatar }}" 
                                         alt="{{ $saving->user->name }}" 
                                         class="rounded-circle me-2" 
                                         width="32" 
                                         height="32">
                                    <div>
                                        <div class="fw-semibold" style="font-size: 0.9rem;">
                                            {{ $saving->user->name }}
                                        </div>
                                        <span class="text-secondary" style="font-size: 0.7rem;">
                                            {{ $saving->user->member_id ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="fw-bold text-success">
                                KES {{ number_format($saving->amount, 2) }}
                            </td>
                            <td>
                                <span class="fw-semibold">{{ $saving->month_name }}</span>
                                <br>
                                <span class="text-secondary" style="font-size: 0.8rem;">{{ $saving->year }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                    {{ ucfirst(str_replace('_', ' ', $saving->payment_method)) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $saving->status_badge }} rounded-pill">
                                    {{ ucfirst($saving->status) }}
                                </span>
                            </td>
                            <td style="font-size: 0.85rem;">
                                {{ $saving->saved_date->format('M d, Y') }}
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group">
                                    <a href="{{ route('savings.show', $saving) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($saving->status === 'pending')
                                        <a href="{{ route('savings.edit', $saving) }}" 
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('savings.approve', $saving) }}" 
                                              method="POST" 
                                              class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-success"
                                                    onclick="return confirm('Approve this saving?')">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('savings.reject', $saving) }}" 
                                              method="POST" 
                                              class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Reject this saving?')">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-piggy-bank fs-1 text-secondary"></i>
                                <p class="text-secondary mt-2">No savings records found</p>
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
                    Showing {{ $savings->firstItem() ?? 0 }} to {{ $savings->lastItem() ?? 0 }} of {{ $savings->total() }} entries
                </span>
                {{ $savings->appends(request()->query())->links() }}
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
</style>
@endpush
@endsection