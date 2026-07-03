@extends('layouts.app')

@section('title', 'Loan Details - ' . ($loan->loan_number ?? ''))

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('loans.index') }}" class="btn btn-link text-decoration-none me-3">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h4 class="fw-bold mb-0">Loan Details</h4>
        <span class="ms-3 badge bg-{{ $loan->status_badge }} rounded-pill fs-6">
            {{ ucfirst($loan->status) }}
        </span>
        @if($loan->isOverdue())
            <span class="ms-2 badge bg-danger rounded-pill fs-6">
                <i class="bi bi-exclamation-triangle me-1"></i> Overdue
            </span>
        @endif
    </div>

    <div class="row">
        <!-- Main Loan Details -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary">Loan Information</h6>
                            <hr>
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Loan Number</p>
                            <p class="fw-bold">{{ $loan->loan_number }}</p>

                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Loan Type</p>
                            <p class="fw-bold">
                                <span class="badge bg-{{ $loan->loan_type == 'instant' ? 'warning' : 'info' }}">
                                    {{ ucfirst($loan->loan_type) }}
                                </span>
                            </p>

                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Amount Taken</p>
                            <p class="fw-bold text-success">KES {{ number_format($loan->amount_taken, 2) }}</p>

                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Interest Rate</p>
                            <p class="fw-bold">{{ $loan->interest_rate }}%</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary">Payment Details</h6>
                            <hr>
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Amount Due</p>
                            <p class="fw-bold text-danger">KES {{ number_format($loan->amount_due, 2) }}</p>

                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Interest Amount</p>
                            <p class="fw-bold">KES {{ number_format($loan->interest_amount, 2) }}</p>

                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Total Payable</p>
                            <p class="fw-bold text-primary">KES {{ number_format($loan->total_amount_payable, 2) }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary">Dates</h6>
                            <hr>
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Date Taken</p>
                            <p class="fw-bold">{{ $loan->date_taken->format('M d, Y H:i') }}</p>

                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Date Due</p>
                            <p class="fw-bold {{ $loan->isOverdue() ? 'text-danger' : '' }}">
                                {{ $loan->date_due->format('M d, Y H:i') }}
                                @if($loan->isOverdue())
                                    <span class="badge bg-danger ms-2">Overdue</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary">Status Information</h6>
                            <hr>
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Status</p>
                            <p class="fw-bold">
                                <span class="badge bg-{{ $loan->status_badge }} rounded-pill">
                                    {{ ucfirst($loan->status) }}
                                </span>
                            </p>

                            @if($loan->approved_at)
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Approved By</p>
                            <p class="fw-bold">{{ $loan->approver->name ?? 'N/A' }}</p>

                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Approved At</p>
                            <p class="fw-bold">{{ $loan->approved_at->format('M d, Y H:i') }}</p>
                            @endif
                        </div>
                    </div>

                    @if($loan->purpose || $loan->notes)
                    <hr>
                    <div class="row">
                        @if($loan->purpose)
                        <div class="col-md-6">
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Purpose</p>
                            <p class="fw-bold">{{ $loan->purpose }}</p>
                        </div>
                        @endif
                        @if($loan->notes)
                        <div class="col-md-6">
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Notes</p>
                            <p class="fw-bold">{{ $loan->notes }}</p>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Payment History -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-clock-history me-2"></i> Payment History
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($loan->payments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 py-2 ps-3">Receipt #</th>
                                    <th class="border-0 py-2">Amount</th>
                                    <th class="border-0 py-2">Balance After</th>
                                    <th class="border-0 py-2">Method</th>
                                    <th class="border-0 py-2">Date</th>
                                    <th class="border-0 py-2">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($loan->payments as $payment)
                                <tr>
                                    <td class="ps-3">
                                        <span class="fw-bold text-primary">{{ $payment->receipt_number }}</span>
                                    </td>
                                    <td class="fw-bold text-success">
                                        KES {{ number_format($payment->amount, 2) }}
                                    </td>
                                    <td>KES {{ number_format($payment->balance_after, 2) }}</td>
                                    <td>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                            {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                        </span>
                                    </td>
                                    <td style="font-size: 0.85rem;">
                                        {{ $payment->payment_date->format('M d, Y') }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $payment->status_badge }} rounded-pill">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="bi bi-receipt text-secondary" style="font-size: 2rem;"></i>
                        <p class="text-secondary mt-2">No payments recorded yet</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar - Actions & Summary -->
        <div class="col-lg-4">
            <!-- Loan Summary -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="bi bi-pie-chart me-2"></i> Loan Summary
                    </h6>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Total Payable</span>
                        <span class="fw-bold">KES {{ number_format($loan->total_amount_payable, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Total Paid</span>
                        <span class="fw-bold text-success">KES {{ number_format($loan->total_paid, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-secondary">Balance</span>
                        <span class="fw-bold text-danger">KES {{ number_format($loan->balance, 2) }}</span>
                    </div>
                    
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" 
                             role="progressbar" 
                             style="width: {{ $loan->payment_progress }}%"
                             aria-valuenow="{{ $loan->payment_progress }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                        </div>
                    </div>
                    <div class="text-center mt-1">
                        <small class="text-secondary">{{ $loan->payment_progress }}% Paid</small>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="bi bi-gear me-2"></i> Actions
                    </h6>

                    @if($loan->status === 'pending')
                        <div class="d-grid gap-2">
                            <form action="{{ route('loans.approve', $loan) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success w-100" onclick="return confirm('Approve this loan?')">
                                    <i class="bi bi-check-circle me-2"></i> Approve Loan
                                </button>
                            </form>
                            <form action="{{ route('loans.cancel', $loan) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Cancel this loan?')">
                                    <i class="bi bi-x-circle me-2"></i> Cancel Loan
                                </button>
                            </form>
                        </div>
                    @endif

                    @if($loan->status === 'approved')
                        <div class="d-grid gap-2">
                            <form action="{{ route('loans.activate', $loan) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Activate and disburse this loan?')">
                                    <i class="bi bi-arrow-right-circle me-2"></i> Activate & Disburse
                                </button>
                            </form>
                            <form action="{{ route('loans.cancel', $loan) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Cancel this loan?')">
                                    <i class="bi bi-x-circle me-2"></i> Cancel Loan
                                </button>
                            </form>
                        </div>
                    @endif

                    @if($loan->status === 'active')
                        <div class="d-grid gap-2">
                            <form action="{{ route('loans.complete', $loan) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success w-100" onclick="return confirm('Mark this loan as completed?')">
                                    <i class="bi bi-check2-circle me-2"></i> Mark as Completed
                                </button>
                            </form>
                            <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                                <i class="bi bi-plus-circle me-2"></i> Record Payment
                            </button>
                        </div>
                    @endif

                    @if(in_array($loan->status, ['pending', 'approved']))
                        <div class="mt-2">
                            <small class="text-secondary">
                                <i class="bi bi-info-circle me-1"></i>
                                This loan needs {{ $loan->status === 'pending' ? 'approval' : 'disbursement' }}
                            </small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Guarantors -->
            @if($loan->guarantors->count() > 0)
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="bi bi-people me-2"></i> Guarantors
                    </h6>
                    @foreach($loan->guarantors as $guarantor)
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                        <div>
                            <div class="fw-semibold">{{ $guarantor->user->name ?? 'N/A' }}</div>
                            <small class="text-secondary">{{ $guarantor->relationship }}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">KES {{ number_format($guarantor->amount_guaranteed, 2) }}</div>
                            <small class="text-secondary">{{ $guarantor->status }}</small>
                        </div>
                    </div>
                    @endforeach
                    <div class="d-flex justify-content-between mt-2">
                        <span class="text-secondary">Total Guaranteed</span>
                        <span class="fw-bold">KES {{ number_format($loan->guarantors_total, 2) }}</span>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Record Payment Modal -->
<div class="modal fade" id="recordPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="#" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Amount *</label>
                        <div class="input-group">
                            <span class="input-group-text">KES</span>
                            <input type="number" step="0.01" class="form-control" name="amount" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method *</label>
                        <select class="form-select" name="payment_method" required>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="mpesa">M-Pesa</option>
                            <option value="cheque">Cheque</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Transaction ID</label>
                        <input type="text" class="form-control" name="transaction_id" placeholder="Optional reference">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i> Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-light {
        background-color: #f8f9fa !important;
    }
    .card {
        transition: all 0.3s ease;
    }
    .badge.fs-6 {
        font-size: 0.9rem !important;
        padding: 0.5rem 1rem;
    }
</style>
@endpush