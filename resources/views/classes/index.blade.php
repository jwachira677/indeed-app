@extends('layouts.app')

@section('title', 'Classes')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Classes & Streams</h4>
            <p class="text-secondary mb-0">Manage all school classes</p>
        </div>
        @can('classes.create')
        <a href="{{ route('classes.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i> Add Class
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
                            <th class="py-3">Stream</th>
                            <th class="py-3">Academic Year</th>
                            <th class="py-3 text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                        <tbody>
                            @forelse ($classes as $index => $class)
                                <tr>
                                    <td class="ps-3">{{ $classes->firstItem() + $index }}</td>
                                    <td class="fw-semibold">{{ $class->class_name }}</td>
                                    <td>{{ $class->stream->name ?? '—' }}</td>
                                    <td>{{ $class->academicYear->year_name ?? 'N/A' }}</td>
                                    <td class="text-end pe-3">
                                        <div class="btn-group">
                                            @can('classes.edit')
                                            <a href="{{ route('classes.edit', $class->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                            @endcan
                                            @can('classes.delete')
                                            <form action="{{ route('classes.destroy', $class->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this class?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-5 text-secondary">No classes found.</td></tr>
                            @endforelse
                        </tbody>
                </table>
            </div>
        </div>
        @if($classes->hasPages())
        <div class="card-footer bg-white border-0 py-3">{{ $classes->links() }}</div>
        @endif
    </div>
</div>
@endsection