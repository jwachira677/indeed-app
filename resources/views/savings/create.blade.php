@extends('layouts.app')

@section('title', 'Record Savings')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('savings.index') }}" class="btn btn-link text-decoration-none me-3">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h4 class="fw-bold mb-0">Record New Savings</h4>
    </div>

    <div class="row">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('savings.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="user_id" class="form-label">Member *</label>
                            <select name="user_id" 
                                    class="form-select @error('user_id') is-invalid @enderror" 
                                    required>
                                <option value="">Select Member</option>
                                @foreach($members ?? [] as $member)
                                    <option value="{{ $member->id }}" {{ old('user_id') == $member->id ? 'selected' : '' }}>
                                        {{ $member->name }} ({{ $member->member_id ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount (KES) *</label>
                            <div class="input-group">
                                <span class="input-group-text">KES</span>
                                <input type="number" 
                                       step="0.01" 
                                       min="0.01"
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       id="amount" 
                                       name="amount" 
                                       value="{{ old('amount') }}" 
                                       placeholder="0.00"
                                       required>
                            </div>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method *</label>
                            <select name="payment_method" 
                                    class="form-select @error('payment_method') is-invalid @enderror" 
                                    required>
                                <option value="">Select Method</option>
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="mpesa" {{ old('payment_method') == 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
                                <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="transaction_id" class="form-label">Transaction ID</label>
                            <input type="text" 
                                   class="form-control @error('transaction_id') is-invalid @enderror" 
                                   id="transaction_id" 
                                   name="transaction_id" 
                                   value="{{ old('transaction_id') }}" 
                                   placeholder="M-Pesa/Transaction reference">
                            @error('transaction_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="2">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Financial Year:</strong> {{ $financialYear ?? App\Models\Saving::getCurrentFinancialYear() }}
                            <br>
                            <small class="text-secondary">Savings will be recorded for the current month ({{ date('F Y') }})</small>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i> Record Savings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection