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
                                       step="0.01" 
                                       min="1"
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       id="amount" 
                                       name="amount" 
                                       value="{{ old('amount', $result['requested_amount'] ?? '') }}" 
                                       placeholder="Enter loan amount"
                                       required>
                            </div>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
            <div class="d-flex justify-content-between align-items-center">
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
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-secondary mb-0" style="font-size: 0.8rem;">Requested Amount</p>
                    <h5 class="fw-bold mb-0">
                        KES {{ number_format($result['requested_amount'], 2) }}
                    </h5>
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
        @if($result['qualifies'])
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong>Congratulations!</strong> 
                {{ $result['user']->name }} qualifies for a loan of 
                <strong>KES {{ number_format($result['requested_amount'], 2) }}</strong>.
                <br>
                <small class="text-success">
                    <i class="bi bi-info-circle me-1"></i>
                    Available savings after active loan: KES {{ number_format($result['available_savings'], 2) }}
                </small>
            </div>
            
            <div class="d-grid gap-2">
                <a href="{{ route('loans.instant') }}" class="btn btn-success">
                    <i class="bi bi-lightning-fill me-2"></i> Apply for Instant Loan
                </a>
                <a href="{{ route('loans.ordinary') }}" class="btn btn-outline-primary">
                    <i class="bi bi-clock-history me-2"></i> Apply for Ordinary Loan
                </a>
            </div>
        @else
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

        <!-- Quick Stats -->
        <hr>
        <div class="row text-center">
            <div class="col-4">
                <p class="text-secondary mb-0" style="font-size: 0.7rem;">Savings</p>
                <span class="fw-bold" style="font-size: 0.9rem;">
                    {{ number_format(($result['total_savings'] / ($result['total_savings'] + $result['requested_amount'])) * 100, 1) }}%
                </span>
            </div>
            <div class="col-4">
                <p class="text-secondary mb-0" style="font-size: 0.7rem;">Loan Request</p>
                <span class="fw-bold" style="font-size: 0.9rem;">
                    {{ number_format(($result['requested_amount'] / ($result['total_savings'] + $result['requested_amount'])) * 100, 1) }}%
                </span>
            </div>
            <div class="col-4">
                <p class="text-secondary mb-0" style="font-size: 0.7rem;">Utilization</p>
                <span class="fw-bold" style="font-size: 0.9rem;">
                    {{ number_format(($result['requested_amount'] / $result['loan_limit']) * 100, 1) }}%
                </span>
            </div>
        </div>
    </div>
</div>
@endif

        </div>
</div>

@push('scripts')
<script>
    // Auto-submit the form when user selects a member (optional)
    document.addEventListener('DOMContentLoaded', function() {
        const userSelect = document.getElementById('user_id');
        const amountInput = document.getElementById('amount');
        
        // If there's a selected user and amount, show results
        @if(isset($result))
            // Scroll to results
            document.querySelector('.col-lg-6.mt-4').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
            });
        @endif
    });

    // Function to increase savings (placeholder)
    function increaseAmount() {
        // You can implement a modal or redirect to savings page
        alert('Please increase savings by saving more in the current financial year.\nVisit the Savings section to add more savings.');
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
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .col-lg-6.mt-4.mt-lg-0 {
            margin-top: 1.5rem !important;
        }
    }
</style>
@endpush
@endsection