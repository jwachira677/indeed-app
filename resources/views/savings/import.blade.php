@extends('layouts.app')

@section('title', 'Import Savings')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('savings.index') }}" class="btn btn-link text-decoration-none me-3">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h4 class="fw-bold mb-0">Import Savings from Excel</h4>
    </div>

    <div class="row">
        <div class="col-md-8 col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Instructions:</strong>
                        <ul class="mb-0 mt-2">
                            <li>File must have member names in the first column</li>
                            <li>Month columns should be named like: Dec, Jan, Feb, etc.</li>
                            <li>"Month 0" or "Dec" will be treated as December of the previous year</li>
                            <li>Amounts can have currency symbols or commas (e.g., "5,000", "KES 5000")</li>
                            <li>Members must already exist in the system</li>
                        </ul>
                    </div>

                    <form method="POST" action="{{ route('import.savings.import') }}" 
                          enctype="multipart/form-data" 
                          id="importForm">
                        @csrf

                        <div class="mb-3">
                            <label for="file" class="form-label">Excel File *</label>
                            <input type="file" 
                                   class="form-control @error('file') is-invalid @enderror" 
                                   id="file" 
                                   name="file" 
                                   accept=".xlsx,.xls,.csv"
                                   required>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-secondary">Supported formats: .xlsx, .xls, .csv (Max: 10MB)</small>
                        </div>

                        <div class="mb-3">
                            <label for="financial_year" class="form-label">Financial Year *</label>
                            <select name="financial_year" 
                                    class="form-select @error('financial_year') is-invalid @enderror" 
                                    required>
                                <option value="">Select Financial Year</option>
                                @foreach($financialYears as $year)
                                    <option value="{{ $year }}" {{ old('financial_year', date('Y').'/'.(date('Y')+1)) == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                            @error('financial_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-secondary">Year when savings will be recorded</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Month Mapping</label>
                            <div class="alert alert-secondary">
                                <small>
                                    <strong>Month 0 / Dec</strong> → December of the financial year start year<br>
                                    <strong>Jan, Feb, Mar...</strong> → January, February, March of the current year
                                </small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <button type="button" id="previewBtn" class="btn btn-info">
                                <i class="bi bi-eye me-2"></i> Preview Data
                            </button>
                        </div>

                        <div id="previewContainer" style="display: none;">
                            <div class="alert alert-secondary mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-database me-2"></i> <span id="previewCount">0</span> rows found</span>
                                    <span class="badge bg-success" id="previewStatus">Loading...</span>
                                </div>
                            </div>
                            <div class="table-responsive mb-3">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Member</th>
                                            <th>Month</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody id="previewTableBody">
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload me-2"></i> Import Savings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-bold mb-0">Excel Format Example</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Member Name</th>
                                    <th>Month 0</th>
                                    <th>Jan</th>
                                    <th>Feb</th>
                                    <th>Mar</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>John Wachira</td>
                                    <td>12722</td>
                                    <td>5000</td>
                                    <td>6000</td>
                                    <td>7000</td>
                                    <td>30722</td>
                                </tr>
                                <tr>
                                    <td>Ken Kagori</td>
                                    <td>9170</td>
                                    <td>15000</td>
                                    <td>5000</td>
                                    <td>5000</td>
                                    <td>34170</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <small class="text-secondary">
                        <strong>Note:</strong> "Month 0" or "Dec" will be treated as December of the previous year.
                        All other months (Jan-Nov) will be in the current year.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('previewBtn').addEventListener('click', function() {
    const fileInput = document.getElementById('file');
    const formData = new FormData();
    
    if (!fileInput.files.length) {
        alert('Please select a file first');
        return;
    }

    const financialYear = document.querySelector('select[name="financial_year"]').value;
    if (!financialYear) {
        alert('Please select a financial year');
        return;
    }

    formData.append('file', fileInput.files[0]);
    formData.append('financial_year', financialYear);

    const previewContainer = document.getElementById('previewContainer');
    const previewBody = document.getElementById('previewTableBody');
    const previewCount = document.getElementById('previewCount');
    const previewStatus = document.getElementById('previewStatus');

    previewContainer.style.display = 'block';
    previewStatus.textContent = 'Loading...';
    previewStatus.className = 'badge bg-warning';

    fetch('{{ route("import.savings.preview") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            previewStatus.textContent = 'Success';
            previewStatus.className = 'badge bg-success';
            previewCount.textContent = data.total_rows;

            previewBody.innerHTML = '';
            let rowNum = 0;
            
            data.preview.forEach((item, index) => {
                rowNum++;
                
                // Carried forward row (if exists)
                if (item.carried_forward > 0) {
                    const cfRow = document.createElement('tr');
                    cfRow.className = 'table-warning';
                    cfRow.innerHTML = `
                        <td>${rowNum}</td>
                        <td><strong>${item.member}</strong></td>
                        <td>0</td>
                        <td><span class="badge bg-info">Opening Balance</span></td>
                        <td>KES ${item.carried_forward.toLocaleString()}</td>
                        <td>KES ${item.carried_forward.toLocaleString()}</td>
                    `;
                    previewBody.appendChild(cfRow);
                    rowNum++;
                }

                // Monthly savings rows - only show each month once
                let runningTotal = item.carried_forward || 0;
                item.savings.forEach((saving, idx) => {
                    runningTotal += saving.amount;
                    const savingRow = document.createElement('tr');
                    savingRow.innerHTML = `
                        <td>${idx === 0 && item.carried_forward === 0 ? rowNum : ''}</td>
                        <td>${idx === 0 && item.carried_forward === 0 ? item.member : ''}</td>
                        <td>${idx === 0 && item.carried_forward > 0 ? 'KES ' + item.carried_forward.toLocaleString() : ''}</td>
                        <td>${saving.month}</td>
                        <td class="text-success">KES ${saving.amount.toLocaleString()}</td>
                        <td class="text-primary">KES ${runningTotal.toLocaleString()}</td>
                    `;
                    previewBody.appendChild(savingRow);
                    if (idx === 0 && item.carried_forward === 0) rowNum++;
                });

                // If no savings and no carried forward
                if (item.savings.length === 0 && item.carried_forward === 0) {
                    const emptyRow = document.createElement('tr');
                    emptyRow.innerHTML = `
                        <td>${rowNum}</td>
                        <td>${item.member}</td>
                        <td>0</td>
                        <td colspan="3" class="text-secondary">No savings recorded</td>
                    `;
                    previewBody.appendChild(emptyRow);
                    rowNum++;
                }
            });
        } else {
            previewStatus.textContent = 'Error';
            previewStatus.className = 'badge bg-danger';
            previewBody.innerHTML = `<tr><td colspan="6" class="text-danger">${data.error}</td></tr>`;
        }
    })
    .catch(error => {
        previewStatus.textContent = 'Error';
        previewStatus.className = 'badge bg-danger';
        previewBody.innerHTML = `<tr><td colspan="6" class="text-danger">${error.message}</td></tr>`;
    });
});
</script>
@endpush
@endsection