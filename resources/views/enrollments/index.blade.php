@extends('layouts.app')

@section('title', 'Enrollments')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Enrollments</h4>
            <p class="text-secondary mb-0">Manage student enrollments</p>
        </div>
        @can('enrollments.create')
        <a href="{{ route('enrollments.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i> Add Enrollment
        </a>
        @endcan
    </div>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col">Student</th>
                            <th scope="col">Class</th>
                            <th scope="col">Enrollment Date</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($enrollments as $enrollment)
                            <tr>
                                <td>
                                    {{ $enrollment->student?->first_name ?? '—' }}
                                    {{ $enrollment->student?->last_name ?? '' }}
                                </td>
                                <td>
                                    {{ $enrollment->schoolClass?->name ?? '—' }}
                                    @if ($enrollment->schoolClass?->gradeLevel)
                                        <span class="text-secondary small">
                                            ({{ $enrollment->schoolClass->gradeLevel->name }})
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    {{ optional($enrollment->enrollment_date ?? $enrollment->created_at)->format('d M, Y') }}
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        @can('enrollments.edit')
                                        <a href="{{ route('enrollments.edit', $enrollment->id) }}"
                                           class="btn btn-sm btn-outline-primary" title="Edit Enrollment">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @endcan
                                        @can('enrollments.delete')
                                        <form action="{{ route('enrollments.destroy', $enrollment->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Are you sure you want to delete this enrollment?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Enrollment">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-secondary py-4">No enrollments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $enrollments->links() }}
    </div>
</div>
@endsection