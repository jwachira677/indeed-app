
@section('title', 'Fine Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('fines.index') }}" class="btn btn-link text-decoration-none me-3">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h4 class="fw-bold mb-0">Fine Details</h4>
        <span class="ms-3 badge bg-{{ $fine->status_badge }} rounded-pill">
            {{ ucfirst(str_replace('_', ' ', $fine->status)) }}
        </span>
        @if($fine->is_overdue)
            <span class="badge bg-danger ms-2">
                {{ $fine->days_overdue }} days overdue
            </span>
        @endif
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Fine Number</p>
                            <p class="fw-bold">{{ $fine->fine_number }}</p>

                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Member</p>
                            <p class="fw-bold">{{ $fine->user->name ?? 'N/A' }}</p>
                            <p class="text-secondary" style="font-size: 0.85rem;">
                                Member ID: {{ $fine->user->member_id ?? 'N/A' }}
                            </p>

                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Reason</p>
                            <p class="fw-bold">{{ $fine->reason }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Amount</p>
                            <p class="fw-bold text-danger fs-5">KES {{ number_format($fine->amount, 2) }}</p>

                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Amount Paid</p>
                            <p class="fw-bold text-success">KES {{ number_format($fine->amount_paid, 2) }}</p>

                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Balance</p>
                            <p class="fw-bold {{ $fine->balance > 0 ? 'text-warning' : 'text-success' }}">
                                KES {{ number_format($fine->balance, 2) }}
                            </p>
                        </div>
                    </div>

                    @if($fine->description)
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Description</p>
                            <p>{{ $fine->description }}</p>
                        </div>
                    </div>
                    @endif

                    <hr>

                    <div class="row">
                        <div class="col-md-4">
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Issued Date</p>
                            <p>{{ $fine->issued_date->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Due Date</p>
                            <p>{{ $fine->due_date ? $fine->due_date->format('M d, Y') : 'No due date' }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Date Paid</p>
                            <p>{{ $fine->date_paid ? $fine->date_paid->format('M d, Y') : 'Not paid' }}</p>
                        </div>
                    </div>

                    @if($fine->approved_by)
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Approved By</p>
                            <p>{{ $fine->approver->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-secondary mb-1" style="font-size: 0.8rem;">Approved At</p>
                            <p>{{ $fine->approved_at ? $fine->approved_at->format('M d, Y H:i') : 'N/A' }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Payment Form -->
            @if(!in_array($fine->status, ['paid', 'waived']))
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="fw-bold">Make Payment</h6>
                    <form method="POST" action="{{ route('fines.pay', $fine) }}">
                        @csrf
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount (KES)</label>
                            <input type="number" 
                                   step="0.01" 
                                   min="0.01" 
                                   max="{{ $fine->balance }}"
                                   class="form-control" 
                                   id="amount" 
                                   name="amount" 
                                   value="{{ $fine->balance }}"
                                   required>
                            <small class="text-secondary">Balance: KES {{ number_format($fine->balance, 2) }}</small>
                        </div>
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="">Select Method</option>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="mpesa">M-Pesa</option>
                                <option value="cheque">Cheque</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="transaction_id" class="form-label">Transaction ID</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="transaction_id" 
                                   name="transaction_id" 
                                   placeholder="M-Pesa/Transaction reference">
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-circle me-2"></i> Record Payment
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <!-- Actions -->
            @if(!in_array($fine->status, ['paid', 'waived']))
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold">Actions</h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('fines.edit', $fine) }}" class="btn btn-secondary">
                            <i class="bi bi-pencil me-2"></i> Edit Fine
                        </a>
                        <form action="{{ route('fines.waive', $fine) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-info w-100" onclick="return confirm('Waive this fine?')">
                                <i class="bi bi-hand-thumbs-up me-2"></i> Waive Fine
                            </button>
                        </form>
                        <form action="{{ route('fines.destroy', $fine) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Delete this fine?')">
                                <i class="bi bi-trash me-2"></i> Delete Fine
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            <!-- Member Summary -->
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="fw-bold">Member Summary</h6>
                    <p class="text-secondary mb-1" style="font-size: 0.8rem;">Total Fines</p>
                    <p class="fw-bold text-danger">KES {{ number_format($fine->user->total_fines ?? 0, 2) }}</p>
                    
                    <p class="text-secondary mb-1" style="font-size: 0.8rem;">Unpaid Fines</p>
                    <p class="fw-bold text-warning">KES {{ number_format($fine->user->total_unpaid_fines ?? 0, 2) }}</p>
                    
                    <p class="text-secondary mb-1" style="font-size: 0.8rem;">Total Fines Count</p>
                    <p class="fw-bold">{{ $fine->user->fines_count ?? 0 }}</p>
                    
                    <a href="{{ route('members.fines', $fine->user) }}" class="btn btn-outline-primary btn-sm mt-2 w-100">
                        <i class="bi bi-list me-2"></i> View All Member Fines
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection