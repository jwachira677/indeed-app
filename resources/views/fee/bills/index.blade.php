@extends('layouts.app')

@section('title', 'Student Fees - ' . $student->first_name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Fee Breakdown</h4>
            <p class="text-secondary mb-0">
                {{ $student->first_name }} {{ $student->last_name }} 
                ({{ $student->admission_number }})
            </p>
        </div>
        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Students
        </a>
    </div>

    <!-- Term Selector -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('students.fees', $student->id) }}" method="GET" class="row g-3 align-items-center">
                <div class="col-auto">
                    <label for="term_id" class="col-form-label fw-semibold">Select Term:</label>
                </div>
                <div class="col-auto">
                    <select name="term_id" id="term_id" class="form-select" onchange="this.form.submit()">
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}" @selected($term->id == $termId)>
                                {{ $term->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    @if($student->currentEnrollment)
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">
                            Class: {{ $student->currentEnrollment->schoolClass->class_name }}
                        </span>
                    @else
                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">
                            No Active Enrollment
                        </span>
                    @endif

                    @if($student->transportRoute)
                        <span class="badge bg-info bg-opacity-10 text-dark border border-info border-opacity-25">
                            Transport: {{ $student->transportRoute->name }}
                        </span>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Fee Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 ps-3">Fee Item</th>
                        <th class="py-3 text-end pe-3" style="width: 200px;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($fees as $fee)
                        <tr>
                            <td class="ps-3">
                                <span class="fw-semibold">{{ $fee['name'] }}</span>
                                @if($fee['description'])
                                    <br><small class="text-muted">{{ $fee['description'] }}</small>
                                @endif
                            </td>
                            <td class="fw-semibold text-end pe-3">
                                {{ number_format($fee['amount'], 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center py-5 text-secondary">
                                No fees have been configured for this term yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($fees->count() > 0)
        <div class="card-footer bg-white border-top-0 py-4 text-end">
            <div class="d-inline-block text-start" style="min-width: 300px;">
                <!-- Removed Optional/Compulsory, just showing the final Total -->
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold fs-5 text-dark">Total:</span>
                    <span class="fw-bold fs-5 text-primary text-end" style="width: 120px;">{{ number_format($grandTotal, 2) }}</span>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection