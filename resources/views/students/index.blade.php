@extends('layouts.app')

@section('title', 'Students')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Students</h4>
        </div>
        @can('students.create')
        <a href="{{ route('students.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i> Add Student
        </a>
        @endcan
    </div>

    <!-- Filter Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('students.index') }}" method="GET" class="row g-3 align-items-end">
                
                <!-- Class Filter -->
                <div class="col-md-4">
                    <label for="class_name" class="form-label text-secondary fw-semibold small">Filter by Class</label>
                    <select name="class_name" id="class_name" class="form-select">
                        <option value="">All Classes</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->class_name }}" @selected(request('class_name') == $class->class_name)>
                                {{ $class->class_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Stream Filter -->
                <div class="col-md-3">
                    <label for="stream_id" class="form-label text-secondary fw-semibold small">Filter by Stream</label>
                    <select name="stream_id" id="stream_id" class="form-select">
                        <option value="">All Streams</option>
                        @foreach ($streams as $stream)
                            <option value="{{ $stream->id }}" @selected(request('stream_id') == $stream->id)>
                                {{ $stream->name }} {{ $stream->code ? "({$stream->code})" : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Buttons -->
                <div class="col-md-5 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter me-1"></i> Filter
                    </button>
                    @if(request()->has('class_name') || request()->has('stream_id'))
                        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 ps-3">Sno.</th>
                            <th class="py-3">Admission Number</th>
                            <th class="py-3">Name</th>
                            <th class="py-3">Current Class</th>
                            <th class="py-3">Date of Birth</th>
                            <th class="py-3">Gender</th>
                            <th class="py-3 text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $index => $student)
                            <tr>
                                <td class="ps-3">{{ $students->firstItem() + $index }}</td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">
                                        {{ $student->admission_number }}
                                    </span>
                                </td>
                                <td class="fw-semibold">{{ $student->first_name }} {{ $student->last_name }}</td>
                                <td>
                                    @if($student->currentEnrollment?->schoolClass)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                            {{ $student->currentEnrollment->schoolClass->class_name }}
                                            @if($student->currentEnrollment->schoolClass->stream)
                                                <span class="text-success opacity-75">| {{ $student->currentEnrollment->schoolClass->stream->name }}</span>
                                            @endif
                                        </span>
                                    @else
                                        <span class="badge bg-light text-secondary border">Not Enrolled</span>
                                    @endif
                                </td>
                                <td>{{ $student->date_of_birth ? $student->date_of_birth->format('d M, Y') : '—' }}</td>
                                <td>
                                    @if($student->gender == 'MALE')
                                        <span class="badge bg-info text-dark">Male</span>
                                    @elseif($student->gender == 'FEMALE')
                                        <span class="badge bg-danger">Female</span>
                                    @else
                                        <span class="badge bg-secondary">Other</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="More actions">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            
                                            @can('enrollments.create')
                                            <li>
                                                <a class="dropdown-item" href="{{ route('students.enroll', $student->id) }}">
                                                    <i class="bi bi-person-plus text-success me-2"></i> Enroll
                                                </a>
                                            </li>
                                            @endcan

                                            @can('students.edit')
                                            <li>
                                                <a class="dropdown-item" href="{{ route('students.edit', $student->id) }}">
                                                    <i class="bi bi-pencil text-primary me-2"></i> Edit
                                                </a>
                                            </li>
                                            <li>
    <a class="dropdown-item" href="{{ route('students.fees', $student->id) }}">
        <i class="bi bi-receipt text-info me-2"></i> View Fees
    </a>
</li>
                                            @endcan
                                            
                                            @can('students.delete')
                                            {{-- <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Delete this student?')">
                                                    @csrf 
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bi bi-trash me-2"></i> Delete
                                                    </button>
                                                </form>
                                            </li> --}}
                                            @endcan

                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-5 text-secondary">No students found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($students->hasPages())
        <div class="card-footer bg-white border-0 py-3">{{ $students->links() }}</div>
        @endif
    </div>
</div>
@endsection