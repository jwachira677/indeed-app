@extends('layouts.app')

@section('title', 'Loan Eligibility Checker')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('dashboard') }}" class="btn btn-link text-decoration-none me-3">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h4 class="fw-bold mb-0">Loan Eligibility Checker</h4>
    </div>

    <div class="row">
        <!-- Eligibility Form -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="bi bi-calculator me-2"></i> Check Loan Eligibility
                    </h6>
                    <p class="text-secondary" style="font-size: 0.9rem;">
                        Enter member details to check if they qualify for a loan.
                        <br>
                        <small>Loan limit is <strong>75%</strong> of total savings.</small>
                    </p>

                    <form method="POST" action="{{ route('loans.eligibility.check') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="user_id" class="form-label">Select Member *</label>
                            <select name="user_id" 
                                    class="form-select @error('user_id') is-invalid @enderror" 
                                    id="user_id"
                                    required>
                                <option value="">-- Select Member --</option>
                                @foreach($members ?? [] as $member)
                                    <option value="{{ $member->id }}" 
                                        {{ (old('user_id', $result['user']->id ?? '') == $member->id) ? 'selected' : '' }}>
                                        {{ $member->name }} 
                                        @if($member->member_id)
                                            ({{ $member->member_id }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Requested Loan Amount (KES) *</label>
                            <div class="input-group">
                                <span class="input-group-text">KES</span>
                                <input type="number" 
                                       step="1" 
                                       min="1"
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       id="amount" 
                                       name="amount" 
                                       value="{{ old('amount', $result['requested_amount'] ?? '') }}" 
                                       placeholder="Enter loan amount"
                                       oninput="updateLoanDetails()"
                                       required>
                            </div>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Loan Type Selection -->
                        <div class="mb-3">
                            <label class="form-label">Loan Type *</label>
                            <div class="d-flex gap-3 flex-wrap">
                                @foreach($loanTypes ?? [] as $type)
                                <div class="form-check flex-grow-1">
                                    <input class="form-check-input" 
                                        type="radio" 
                                        name="loan_type" 
                                        id="loan_{{ $type->slug }}" 
                                        value="{{ $type->slug }}"
                                        {{ old('loan_type', $result['loan_type']->slug ?? '') == $type->slug ? 'checked' : '' }}
                                        data-interest="{{ $type->interest_rate }}"
                                        data-slug="{{ $type->slug }}"
                                        onchange="updateLoanDetails()">
                                    <label class="form-check-label" for="loan_{{ $type->slug }}">
                                        <span class="fw-semibold">{{ $type->name }}</span>
                                        <br>
                                        <small class="text-secondary">
                                            @if($type->slug == 'instant')
                                                7 days | {{ number_format($type->interest_rate, 2) }}% interest
                                            @else
                                                Tiered duration | {{ number_format($type->interest_rate, 2) }}% interest
                                            @endif
                                        </small>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            @error('loan_type')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Read-only Loan Details -->
                        <div class="bg-light rounded-3 p-3 mb-3">
                            <h6 class="fw-bold text-secondary mb-2" style="font-size: 0.8rem;">
                                <i class="bi bi-info-circle me-1"></i> Loan Details
                            </h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="text-secondary" style="font-size: 0.75rem;">Duration</label>
                                    <input type="text" 
                                           id="duration_display" 
                                           class="form-control form-control-sm bg-white" 
                                           value="-- Select loan type --"
                                           readonly>
                                </div>
                                <div class="col-6">
                                    <label class="text-secondary" style="font-size: 0.75rem;">Due Date</label>
                                    <input type="text" 
                                           id="due_date_display" 
                                           class="form-control form-control-sm bg-white" 
                                           value="--"
                                           readonly>
                                </div>
                                <div class="col-6">
                                    <label class="text-secondary" style="font-size: 0.75rem;">Interest Rate</label>
                                    <input type="text" 
                                           id="interest_display" 
                                           class="form-control form-control-sm bg-white" 
                                           value="--"
                                           readonly>
                                </div>
                                <div class="col-6">
                                    <label class="text-secondary" style="font-size: 0.75rem;">Total Payable</label>
                                    <input type="text" 
                                           id="total_payable_display" 
                                           class="form-control form-control-sm bg-white" 
                                           value="--"
                                           readonly>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-circle me-2"></i> Check Eligibility
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Results Section -->
        <div class="col-lg-6 mt-4 mt-lg-0">
            @if(isset($result))
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="bi bi-graph-up-arrow me-2"></i> Eligibility Results
                    </h6>

                    <!-- Loan Type Info -->
                    <div class="alert alert-info">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <strong>{{ $result['loan_type']->name ?? 'N/A' }}</strong>
                                <br>
                                <small>
                                    Duration: {{ $result['duration_label'] ?? $result['duration_days'] . ' days' }}
                                    | Due: {{ isset($result['due_date']) ? $result['due_date']->format('M d, Y') : 'N/A' }}
                                    | Rate: {{ $result['interest_rate'] ?? 0 }}%
                                </small>
                            </div>
                            <span class="badge bg-primary">
                                Interest: KES {{ number_format($result['interest_amount'] ?? 0, 2) }}
                            </span>
                        </div>
                    </div>

                    <!-- Financial Summary -->
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="bg-light rounded-3 p-3 text-center">
                                <p class="text-secondary mb-1" style="font-size: 0.75rem;">Total Savings</p>
                                <h5 class="fw-bold text-primary mb-0">
                                    KES {{ number_format($result['total_savings'], 2) }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded-3 p-3 text-center">
                                <p class="text-secondary mb-1" style="font-size: 0.75rem;">Active Loan Balance</p>
                                <h5 class="fw-bold text-danger mb-0">
                                    - KES {{ number_format($result['active_loan_balance'], 2) }}
                                </h5>
                            </div>
                        </div>
                    </div>

                    <!-- Available Savings -->
                    <div class="bg-primary bg-opacity-10 rounded-3 p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <p class="text-secondary mb-0" style="font-size: 0.8rem;">Available Savings</p>
                                <h5 class="fw-bold text-primary mb-0">
                                    KES {{ number_format($result['available_savings'], 2) }}
                                </h5>
                            </div>
                            <div>
                                <p class="text-secondary mb-0" style="font-size: 0.8rem;">Loan Limit (75%)</p>
                                <h5 class="fw-bold text-info mb-0">
                                    KES {{ number_format($result['loan_limit'], 2) }}
                                </h5>
                            </div>
                        </div>
                    </div>

                    <!-- Requested Amount -->
                    <div class="bg-light rounded-3 p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <p class="text-secondary mb-0" style="font-size: 0.8rem;">Requested Amount</p>
                                <h5 class="fw-bold mb-0">
                                    KES {{ number_format($result['requested_amount'], 2) }}
                                </h5>
                                @if(isset($result['total_payable']))
                                <small class="text-secondary">
                                    + Interest: KES {{ number_format($result['interest_amount'] ?? 0, 2) }}
                                    = KES {{ number_format($result['total_payable'], 2) }}
                                </small>
                                @endif
                            </div>
                            <div class="text-end">
                                <p class="text-secondary mb-0" style="font-size: 0.8rem;">Eligibility</p>
                                @if($result['qualifies'])
                                    <span class="badge bg-success rounded-pill fs-6">
                                        <i class="bi bi-check-circle me-1"></i> Qualified
                                    </span>
                                @else
                                    <span class="badge bg-danger rounded-pill fs-6">
                                        <i class="bi bi-x-circle me-1"></i> Not Qualified
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Result Message -->
<!-- Result Message -->
@if($result['qualifies'])
    <div class="alert alert-success">
        <i class="bi bi-check-circle-fill me-2"></i>
        <strong>{{ $result['user']->name }}</strong> qualifies for a <strong>{{ $result['loan_type']->name }}</strong> of 
        <strong>KES {{ number_format($result['requested_amount'], 2) }}</strong>.
        <br>
        <small class="text-success">
            <i class="bi bi-info-circle me-1"></i>
            Duration: {{ $result['duration_label'] ?? $result['duration_days'] . ' days' }}
            | Due: {{ $result['due_date']->format('M d, Y') }}
            | Available savings after active loan: KES {{ number_format($result['available_savings'], 2) }}
        </small>
    </div>
    
    <!-- Apply Form -->
    <div class="card border-0 bg-light">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Apply for this Loan</h6>
            <form method="POST" action="{{ route('loans.apply') }}">
                @csrf
                <input type="hidden" name="user_id" value="{{ $result['user']->id }}">
                <input type="hidden" name="amount" value="{{ $result['requested_amount'] }}">
                <input type="hidden" name="loan_type" value="{{ $result['loan_type']->slug }}">

                <div class="mb-2">
                    <label for="purpose" class="form-label" style="font-size: 0.85rem;">Purpose of Loan</label>
                    <input type="text" 
                           class="form-control form-control-sm" 
                           id="purpose" 
                           name="purpose" 
                           placeholder="e.g., Business expansion, Medical expenses, etc."
                           required>
                </div>

                <div class="mb-2">
                    <label for="notes" class="form-label" style="font-size: 0.85rem;">Additional Notes</label>
                    <textarea class="form-control form-control-sm" 
                              id="notes" 
                              name="notes" 
                              rows="2" 
                              placeholder="Any additional information..."></textarea>
                </div>

                <button type="submit" class="btn btn-success w-100">
                    <i class="bi bi-check2-circle me-2"></i> Submit Application
                </button>
            </form>
        </div>
    </div>
@else
    <!-- Not qualified message -->
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <div>
            <strong>Not Qualified</strong>
            <ul class="mb-0 mt-2" style="padding-left: 1.2rem;">
                <li>Requested: KES {{ number_format($result['requested_amount'], 2) }}</li>
                <li>Loan Limit: KES {{ number_format($result['loan_limit'], 2) }}</li>
                <li class="text-danger">
                    <strong>Shortfall: KES {{ number_format($result['shortfall'], 2) }}</strong>
                </li>
            </ul>
        </div>
    </div>
    
    <div class="alert alert-info">
        <i class="bi bi-lightbulb me-2"></i>
        <strong>How to qualify:</strong>
        <ul class="mb-0 mt-1" style="padding-left: 1.2rem;">
            <li>Increase your savings by KES {{ number_format($result['shortfall'] / 0.75, 2) }}</li>
            <li>Or reduce your active loan balance by KES {{ number_format($result['shortfall'], 2) }}</li>
        </ul>
    </div>
@endif


                </div>
            </div>
            @else
            <!-- Empty State -->
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-calculator-fill text-secondary" style="font-size: 4rem;"></i>
                    <h5 class="fw-bold mt-3">No Results Yet</h5>
                    <p class="text-secondary" style="font-size: 0.9rem;">
                        Select a member, choose loan type, and enter the loan amount<br>
                        to check eligibility.
                    </p>
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <span class="badge bg-primary">75% Limit</span>
                        <span class="badge bg-success">Instant - 7 Days</span>
                        <span class="badge bg-info">Ordinary - Tiered</span>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Get loan types data from PHP
    const loanTypesData = @json($loanTypes);

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize loan details
        updateLoanDetails();
        
        // If there's a result, scroll to it
        @if(isset($result))
            document.querySelector('.col-lg-6.mt-4').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
            });
        @endif

        // Add event listener for amount input
        const amountInput = document.getElementById('amount');
        if (amountInput) {
            amountInput.addEventListener('input', updateLoanDetails);
        }
    });

    function updateLoanDetails() {
        const amount = parseFloat(document.getElementById('amount').value) || 0;
        const selectedRadio = document.querySelector('input[name="loan_type"]:checked');
        
        if (!selectedRadio) {
            // No loan type selected
            document.getElementById('duration_display').value = '-- Select loan type --';
            document.getElementById('due_date_display').value = '--';
            document.getElementById('interest_display').value = '--';
            document.getElementById('total_payable_display').value = '--';
            return;
        }

        const loanSlug = selectedRadio.dataset.slug;
        const interestRate = parseFloat(selectedRadio.dataset.interest) || 0;
        
        // Find the loan type object
        const loanType = loanTypesData.find(type => type.slug === loanSlug);
        
        if (!loanType) {
            return;
        }

        let durationDays = 0;
        let durationLabel = '';

        if (loanSlug === 'instant') {
            durationDays = 7;
            durationLabel = '7 days (Instant)';
        } else if (loanSlug === 'ordinary') {
            // For ordinary loans, find the tier based on amount
            if (amount > 0) {
                // Get duration tiers from the loan type
                const tiers = @json($loanTypes->where('slug', 'ordinary')->first()->durations ?? []);
                const tier = tiers.find(t => amount >= t.min_amount && amount <= t.max_amount);
                if (tier) {
                    durationDays = tier.duration_days;
                    durationLabel = tier.duration_days + ' days (Tier)';
                } else {
                    durationDays = 30;
                    durationLabel = '30 days (Default)';
                }
            } else {
                document.getElementById('duration_display').value = 'Enter amount to calculate';
                document.getElementById('due_date_display').value = '--';
                document.getElementById('interest_display').value = interestRate + '%';
                document.getElementById('total_payable_display').value = '--';
                return;
            }
        }

        // Calculate due date
        const dueDate = new Date();
        dueDate.setDate(dueDate.getDate() + durationDays);
        const dateString = dueDate.toLocaleDateString('en-US', { 
            weekday: 'short', 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });

        // Update displays
        document.getElementById('duration_display').value = durationLabel || '--';
        document.getElementById('due_date_display').value = dateString;
        document.getElementById('interest_display').value = interestRate + '%';

        // Calculate total payable
        if (amount > 0 && interestRate > 0) {
            const interestAmount = (amount * interestRate) / 100;
            const totalPayable = amount + interestAmount;
            document.getElementById('total_payable_display').value = 'KES ' + totalPayable.toFixed(2);
        } else {
            document.getElementById('total_payable_display').value = 'KES 0.00';
        }
    }
</script>
@endpush

@push('styles')
<style>
    .hover-shadow {
        transition: all 0.3s ease;
    }
    .hover-shadow:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.08) !important;
    }
    
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
    
    .form-check {
        padding: 0.75rem 1rem;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        transition: all 0.2s ease;
        flex: 1;
        min-width: 150px;
    }
    
    .form-check:hover {
        border-color: #0d6efd;
        background-color: #f8f9fa;
    }
    
    .form-check .form-check-input:checked + .form-check-label {
        color: #0d6efd;
    }
    
    .form-check:has(.form-check-input:checked) {
        border-color: #0d6efd;
        background-color: #f0f7ff;
    }
    
    @media (max-width: 768px) {
        .col-lg-6.mt-4.mt-lg-0 {
            margin-top: 1.5rem !important;
        }
        .form-check {
            flex: 1 1 100%;
        }
    }
</style>
@endpush
@endsection