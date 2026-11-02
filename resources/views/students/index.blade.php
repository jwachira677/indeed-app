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

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 ps-3">Sno.</th>
                            <th class="py-3">Admission Number</th>
                            <th class="py-3">Name</th>
                            <th class="py-3">Date of Birth</th>
                            <th class="py-3">Gender</th>
                            <th class="py-3 text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $index => $student)
                            <tr>
                                <td class="ps-3">{{ $students->firstItem() + $index }}</td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">{{ $student->admission_number }}</span></td>
                                <td class="fw-semibold">{{ $student->first_name }} {{ $student->last_name }}</td>
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
                                    <div class="btn-group">
                                        <!-- Enroll Button -->
                                        @can('enrollments.create')
                                        <a href="{{ route('students.enroll', $student->id) }}" class="btn btn-sm btn-outline-success" title="Assign to Class">
                                            <i class="bi bi-person-plus"></i> Enroll
                                        </a>
                                        @endcan

                                        @can('students.edit')
                                        <a href="{{ route('students.edit', $student->id) }}" class="btn btn-sm btn-outline-primary" title="Edit Student">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('students.delete')
                                        <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this student?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Student">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-5 text-secondary">No students found.</td></tr>
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