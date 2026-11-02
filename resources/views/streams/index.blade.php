@extends('layouts.app')

@section('title', 'Streams')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Streams</h4>
            <p class="text-secondary mb-0">Manage class streams (e.g., East, West, North)</p>
        </div>
        <a href="{{ route('streams.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i> Add Stream
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 ps-3">Sno.</th>
                            <th class="py-3">Name</th>
                            <th class="py-3">Code</th>
                            <th class="py-3 text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($streams as $index => $stream)
                            <tr>
                                <td class="ps-3">{{ $streams->firstItem() + $index }}</td>
                                <td class="fw-semibold">{{ $stream->name }}</td>
                                <td>{{ $stream->code ?: '—' }}</td>
                                <td class="text-end pe-3">
                                    <div class="btn-group">
                                        <a href="{{ route('streams.edit', $stream->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                        <form action="{{ route('streams.destroy', $stream->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this stream?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-5 text-secondary">No streams found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($streams->hasPages())
        <div class="card-footer bg-white border-0 py-3">{{ $streams->links() }}</div>
        @endif
    </div>
</div>
@endsection