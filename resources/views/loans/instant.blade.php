@extends('layouts.app')

@section('title', 'Instant Loans')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('loans.eligibility.form') }}" class="btn btn-link text-decoration-none me-3">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h4 class="fw-bold mb-0">Instant Loans</h4>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-lightning-fill text-warning" style="font-size: 4rem;"></i>
            <h5 class="fw-bold mt-3">Instant Loan Application</h5>
            <p class="text-secondary">
                This feature is coming soon. <br>
                Please check back later.
            </p>
            <a href="{{ route('loans.eligibility.form') }}" class="btn btn-primary">
                <i class="bi bi-arrow-left me-2"></i> Check Eligibility
            </a>
        </div>
    </div>
</div>
@endsection