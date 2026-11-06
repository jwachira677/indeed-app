@extends('layouts.app')

@section('title', 'Vehicles')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">School Vehicles</h4>
            <p class="text-secondary mb-0">Manage buses, vans, and their assigned drivers</p>
        </div>
        @can('vehicles.create')
        <a href="{{ route('vehicles.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i> Add Vehicle
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
                            <th class="py-3">Reg Number</th>
                            <th class="py-3">Model</th>
                            <th class="py-3">Capacity</th>
                            <th class="py-3">Assigned Driver</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($vehicles as $index => $vehicle)
                            <tr>
                                <td class="ps-3">{{ $vehicles->firstItem() + $index }}</td>
                                <td><span class="badge bg-dark">{{ $vehicle->registration_number }}</span></td>
                                <td class="fw-semibold">{{ $vehicle->model }}</td>
                                <td>{{ $vehicle->capacity }} Seats</td>
                                <td>
                                    @if($vehicle->driver)
                                        <i class="bi bi-person-circle text-secondary me-1"></i> {{ $vehicle->driver->name }}
                                    @else
                                        <span class="text-muted fst-italic">Unassigned</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $vehicle->status_badge }} rounded-pill">
                                        {{ ucfirst(strtolower($vehicle->status)) }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group">
                                        @can('vehicles.update')
                                        <a href="{{ route('vehicles.edit', $vehicle->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                        @endcan
                                        
                                        @can('vehicles.delete')
                                        <form action="{{ route('vehicles.destroy', $vehicle->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this vehicle?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-5 text-secondary">No vehicles registered.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($vehicles->hasPages())
        <div class="card-footer bg-white border-0 py-3">{{ $vehicles->links() }}</div>
        @endif
    </div>
</div>
@endsection