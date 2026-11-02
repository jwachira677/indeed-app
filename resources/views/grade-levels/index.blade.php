@extends('layouts.app')

@section('title', 'Grade Levels')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Grade Levels</h4>
            <p class="text-secondary mb-0">Manage the CBC grade levels</p>
        </div>
        @can('grade-levels.create')
        <a href="{{ route('grade-levels.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i> Add Grade Level
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
                            <th class="py-3">Code</th>
                            <th class="py-3">Name</th>
                            <th class="py-3">Level Group</th>
                            <th class="py-3">Sort Order</th>
                            <th class="py-3 text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($gradeLevels as $index => $level)
                            <tr>
                                <td class="ps-3">{{ $gradeLevels->firstItem() + $index }}</td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">{{ $level->code }}</span></td>
                                <td class="fw-semibold">{{ $level->name }}</td>
                                <td>{{ $level->level_group_name }}</td>
                                <td>{{ $level->sort_order }}</td>
                                <td class="text-end pe-3">
                                    <div class="btn-group">
                                        @can('grade-levels.edit')
                                        <a href="{{ route('grade-levels.edit', $level->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                        @endcan
                                        @can('grade-levels.delete')
                                        <form action="{{ route('grade-levels.destroy', $level->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this grade level?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-5 text-secondary">No grade levels found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($gradeLevels->hasPages())
        <div class="card-footer bg-white border-0 py-3">{{ $gradeLevels->links() }}</div>
        @endif
    </div>
</div>
@endsection