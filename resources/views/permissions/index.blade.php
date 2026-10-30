{{-- Change 'layouts.app' / 'content' to match your layout. --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Permissions</h1>
            <p class="text-muted mb-0">Choose a role, tick what it may do, then save.</p>
        </div>
    </div>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">

        {{-- Role selector --}}
        <div class="col-lg-3">
            <div class="list-group shadow-sm">
                @foreach ($roles as $r)
                    <a href="{{ route('permissions', ['role' => $r->slug]) }}"
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $r->id === $role->id ? 'active' : '' }}">
                        <span>{{ $r->name }}</span>
                        @if ($r->isAdmin())
                            <i class="bi bi-lock-fill" title="Full access, not editable"></i>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Matrix --}}
        <div class="col-lg-9">
            @if ($role->isAdmin())
                <div class="alert alert-info">
                    <i class="bi bi-shield-check me-1"></i>
                    <strong>Administrator</strong> always has full access. This role can't be edited, so nobody can lock themselves out.
                </div>
            @endif

            <form method="POST" action="{{ route('permissions.update', $role) }}">
                @csrf
                @method('PUT')

                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong>{{ $role->name }}</strong>
                        @unless ($role->isAdmin())
                            @can('permissions.update')
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-check2-circle me-1"></i> Save changes
                                </button>
                            @endcan
                        @endunless
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="permission-matrix">
                            <thead class="table-light">
                                <tr>
                                    <th>Module</th>
                                    @foreach ($actions as $action)
                                        <th class="text-center text-capitalize">
                                            {{ $action }}
                                            @unless ($role->isAdmin())
                                                <div>
                                                    <input type="checkbox" class="form-check-input col-toggle"
                                                           data-action="{{ $action }}" title="Toggle column">
                                                </div>
                                            @endunless
                                        </th>
                                    @endforeach
                                    <th class="text-center">All</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($modules as $module => $definition)
                                    <tr data-module="{{ $module }}">
                                        <td class="fw-medium">{{ $definition['label'] }}</td>

                                        @foreach ($actions as $action)
                                            @php
                                                $id = $permissionIds["{$module}.{$action}"] ?? null;
                                                $checked = $role->isAdmin() || ($id && in_array($id, $granted));
                                            @endphp
                                            <td class="text-center">
                                                @if ($id)
                                                    <input type="checkbox"
                                                           class="form-check-input perm-box"
                                                           data-action="{{ $action }}"
                                                           name="permissions[]"
                                                           value="{{ $id }}"
                                                           @checked($checked)
                                                           @disabled($role->isAdmin())>
                                                @else
                                                    <span class="text-muted">&ndash;</span>
                                                @endif
                                            </td>
                                        @endforeach

                                        <td class="text-center">
                                            @unless ($role->isAdmin())
                                                <input type="checkbox" class="form-check-input row-toggle" title="Toggle row">
                                            @endunless
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const table = document.getElementById('permission-matrix');
        if (!table) return;

        const boxes = (scope) => Array.from(scope.querySelectorAll('.perm-box'));

        // Row toggle
        table.querySelectorAll('.row-toggle').forEach(toggle => {
            toggle.addEventListener('change', () => {
                boxes(toggle.closest('tr')).forEach(b => b.checked = toggle.checked);
                syncToggles();
            });
        });

        // Column toggle
        table.querySelectorAll('.col-toggle').forEach(toggle => {
            toggle.addEventListener('change', () => {
                boxes(table).filter(b => b.dataset.action === toggle.dataset.action)
                            .forEach(b => b.checked = toggle.checked);
                syncToggles();
            });
        });

        table.addEventListener('change', e => {
            if (e.target.classList.contains('perm-box')) syncToggles();
        });

        // Keep row/column toggles in step with the individual boxes
        function syncToggles() {
            table.querySelectorAll('tbody tr').forEach(tr => {
                const b = boxes(tr), t = tr.querySelector('.row-toggle');
                if (t) t.checked = b.length > 0 && b.every(x => x.checked);
            });
            table.querySelectorAll('.col-toggle').forEach(t => {
                const b = boxes(table).filter(x => x.dataset.action === t.dataset.action);
                t.checked = b.length > 0 && b.every(x => x.checked);
            });
        }
        syncToggles();
    })();
</script>
@endpush
