<!-- resources/views/components/sidebar.blade.php -->
@php
    $menu = \App\Support\Menu::for(auth()->user());
@endphp

<aside class="sidebar d-flex flex-column flex-shrink-0 p-0" style="width: 280px; height: 100vh; position: sticky; top: 0; background: linear-gradient(180deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);">
    {{-- Sidebar Brand --}}
    <div class="sidebar-brand d-flex align-items-center px-4 py-4 border-bottom border-secondary border-opacity-25">
        <a href="/" class="d-flex align-items-center text-white text-decoration-none">
            <div class="brand-icon bg-primary bg-gradient rounded-3 p-2 me-2">
                <i class="bi bi-mortarboard-fill fs-4"></i>
            </div>
            <div>
                <span class="fs-5 fw-bold text-white">School</span>
                <span class="d-block small text-secondary" style="font-size: 0.65rem; letter-spacing: 1px;">CBC ADMIN PANEL</span>
            </div>
        </a>
    </div>

    {{-- Sidebar Navigation: built from config/menu.php, filtered by the user's permissions --}}
    <div class="sidebar-nav flex-grow-1 overflow-auto px-3 py-4">
        <ul class="nav nav-pills flex-column mb-4">
            @include('partials.menu', ['items' => $menu, 'level' => 0, 'prefix' => 'menu'])
        </ul>
    </div>

    {{-- User Profile Section at Bottom --}}
    <div class="sidebar-footer border-top border-secondary border-opacity-25 p-3">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none p-2 rounded-3 hover-bg-light disabled-link">
            <div class="position-relative">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=0D6EFD&color=fff&size=50&bold=true"
                     alt="Profile"
                     width="40"
                     height="40"
                     class="rounded-circle border border-2 border-primary">
                <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-dark rounded-circle"
                      style="width: 12px; height: 12px;"></span>
            </div>
            <div class="ms-2 flex-grow-1">
                <div class="fw-semibold text-white" style="font-size: 0.9rem;">
                    {{ auth()->user()->name ?? 'Guest User' }}
                </div>
                <span class="text-secondary" style="font-size: 0.75rem;">
                    {{ auth()->user()->email ?? 'guest@example.com' }}
                </span>
            </div>
            <i class="bi bi-three-dots-vertical text-secondary"></i>
        </a>
    </div>
</aside>

{{-- Small style block for disabled links and animations --}}
<style>
    .disabled-link { cursor: not-allowed; opacity: 0.55; }
    .disabled-link:hover { background: transparent !important; }
    .nav-link.active { background: rgba(13, 110, 253, 0.15) !important; color: #fff !important; font-weight: 500; }
    .nav-link:hover:not(.disabled-link) { background: rgba(255,255,255,0.05); }
    .collapse .nav-link { font-size: 0.875rem; }
    .transition-transform { transition: transform 0.2s; }

    /* Rotate the chevron when the parent menu is open */
    [aria-expanded="true"] > .bi-chevron-down { transform: rotate(180deg); }

    /* Subtle adjustments for the nested lists */
    .collapse ul .nav-link { padding-left: 10px; }
</style>
