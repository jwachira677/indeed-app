{{-- Recursive menu renderer. Expects: $items, $level (0 = top), $prefix --}}
@foreach ($items as $item)
    @php
        $hasChildren = ! empty($item['children']);
        $nodeId = $prefix . '-' . \Illuminate\Support\Str::slug($item['label']);
        $linkPadding = $level === 0 ? '' : ($level === 1 ? 'py-2' : 'py-1');
        $iconClass = $level === 0 ? 'me-3' : 'me-2 text-primary';
    @endphp

    <li class="nav-item {{ $level === 0 ? 'mb-1' : '' }}">
        @if ($hasChildren)
            <a class="nav-link {{ $linkPadding }} text-white-50 d-flex align-items-center {{ $item['active'] ? '' : 'collapsed' }}"
               data-bs-toggle="collapse" href="#{{ $nodeId }}" role="button"
               aria-expanded="{{ $item['active'] ? 'true' : 'false' }}" aria-controls="{{ $nodeId }}">
                <i class="bi {{ $item['icon'] ?? 'bi-circle' }} {{ $iconClass }}"></i>
                <span>{{ $item['label'] }}</span>
                <i class="bi bi-chevron-down ms-auto transition-transform" @if ($level > 0) style="font-size: 0.75rem;" @endif></i>
            </a>
            <div class="collapse {{ $item['active'] ? 'show' : '' }}" id="{{ $nodeId }}">
                <ul class="nav nav-pills flex-column {{ $level === 0 ? 'ms-4' : 'ms-3' }} mt-1">
                    @include('partials.menu', ['items' => $item['children'], 'level' => $level + 1, 'prefix' => $nodeId])
                </ul>
            </div>
        @elseif ($item['url'])
            <a href="{{ $item['url'] }}"
               class="nav-link {{ $linkPadding }} text-white-50 {{ $item['active'] ? 'active' : '' }}">
                <i class="bi {{ $item['icon'] ?? 'bi-circle' }} {{ $iconClass }}"></i> {{ $item['label'] }}
                @if (! empty($item['badge']))
                    <span class="badge bg-primary bg-opacity-25 text-primary rounded-pill ms-auto">{{ $item['badge'] }}</span>
                @endif
            </a>
        @else
            <a href="#" class="nav-link {{ $linkPadding }} text-white-50 disabled-link" title="Coming soon">
                <i class="bi {{ $item['icon'] ?? 'bi-circle' }} {{ $iconClass }}"></i> {{ $item['label'] }}
            </a>
        @endif
    </li>
@endforeach
