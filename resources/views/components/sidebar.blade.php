<!-- resources/views/components/sidebar.blade.php -->
<aside class="sidebar d-flex flex-column flex-shrink-0 p-0" style="width: 280px; height: 100vh; position: sticky; top: 0; background: linear-gradient(180deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);">
    {{-- Sidebar Brand --}}
    <div class="sidebar-brand d-flex align-items-center px-4 py-4 border-bottom border-secondary border-opacity-25">
        <a href="/" class="d-flex align-items-center text-white text-decoration-none">
            <div class="brand-icon bg-primary bg-gradient rounded-3 p-2 me-2">
                <i class="bi bi-boxes fs-4"></i>
            </div>
            <div>
                <span class="fs-5 fw-bold text-white">Laravel</span>
                <span class="d-block small text-secondary" style="font-size: 0.65rem; letter-spacing: 1px;">ADMIN PANEL</span>
            </div>
        </a>
    </div>

    {{-- Sidebar Navigation --}}
    <div class="sidebar-nav flex-grow-1 overflow-auto px-3 py-4">
        <p class="text-uppercase text-secondary small fw-bold px-3 mb-3" style="font-size: 0.65rem; letter-spacing: 0.5px;">Main Menu</p>
        
        <ul class="nav nav-pills flex-column mb-4">
            {{-- Dashboard --}}
            <li class="nav-item mb-1">
                <a href="{{ route('dashboard') }}" class="nav-link text-white-50 {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill me-3"></i>
                    <span>Dashboard</span>
                    <span class="badge bg-primary bg-opacity-25 text-primary ms-auto">New</span>
                </a>
            </li>

            {{-- Analytics --}}
            <li class="nav-item mb-1">
                <a href="{{ route('analytics') }}" class="nav-link text-white-50 {{ request()->routeIs('analytics') ? 'active' : '' }}">
                    <i class="bi bi-graph-up-arrow me-3"></i>
                    <span>Analytics</span>
                    <span class="badge bg-success bg-opacity-25 text-success ms-auto">+12%</span>
                </a>
            </li>
        </ul>

        <p class="text-uppercase text-secondary small fw-bold px-3 mb-3" style="font-size: 0.65rem; letter-spacing: 0.5px;">Management</p>
        
        <ul class="nav nav-pills flex-column mb-4">
            {{-- Users --}}
            <li class="nav-item mb-1">
                <a href="{{ route('users.index') }}" class="nav-link text-white-50 {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="bi bi-people me-3"></i>
                    <span>Users</span>
                    <span class="badge bg-danger rounded-pill ms-auto">24</span>
                </a>
            </li>

            {{-- Products with dropdown --}}
            <li class="nav-item mb-1">
                <a class="nav-link text-white-50 {{ request()->routeIs('loans.*') ? 'active' : '' }}" 
                data-bs-toggle="collapse" 
                href="#loansMenu" 
                role="button" 
                aria-expanded="{{ request()->routeIs('loans.*') ? 'true' : 'false' }}">
                    <i class="bi bi-coin me-3"></i>
                    <span>Loans</span>
                    <i class="bi bi-chevron-down ms-auto transition-transform"></i>
                </a>
<div class="collapse {{ request()->routeIs('loans.*') ? 'show' : '' }}" id="loansMenu">
    <ul class="nav nav-pills flex-column ms-4 mt-1">
        <li class="nav-item">
            <a href="{{ route('loans.index') }}" class="nav-link py-2 text-white-50 {{ request()->routeIs('loans.index') ? 'active' : '' }}">
                <i class="bi bi-list-ul me-2 text-primary"></i> All Loans
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('loans.eligibility.form') }}" class="nav-link py-2 text-white-50 {{ request()->routeIs('loans.eligibility.*') ? 'active' : '' }}">
                <i class="bi bi-calculator me-2 text-success"></i> Check Eligibility
            </a>
        </li>
<li class="nav-item">
    <a href="{{ route('loans.by-status', 'active') }}" class="nav-link py-2 text-white-50 {{ request()->routeIs('loans.by-status') && request()->route('status') == 'active' ? 'active' : '' }}">
        <i class="bi bi-play-circle me-2 text-success"></i> Active Loans
        <span class="badge bg-success bg-opacity-25 text-success rounded-pill ms-auto">
            {{ App\Models\Loan::active()->count() }}
        </span>
    </a>
</li>


    </ul>
</div>
            </li>
            <li class="nav-item mb-1">
    <a href="{{ route('fines.index') }}" 
       class="nav-link text-white-50 {{ request()->routeIs('fines.*') ? 'active' : '' }}">
        <i class="bi bi-exclamation-triangle me-3"></i>
        <span>Fines</span>
        <span class="badge bg-danger bg-opacity-25 text-danger rounded-pill ms-auto">
            {{ App\Models\Fine::pending()->count() + App\Models\Fine::overdue()->count() }}
        </span>
    </a>
</li>




            {{-- Savings --}}
            <li class="nav-item mb-1">
                <a href="{{ route('savings.index') }}" class="nav-link text-white-50 {{ request()->routeIs('savings') ? 'active' : '' }}">
                    <i class="bi bi-piggy-bank me-3"></i>
                    <span>Savings</span>
                    <i class="bi bi-coin text-warning ms-auto"></i>
                </a>
            </li>
        </ul>

        <p class="text-uppercase text-secondary small fw-bold px-3 mb-3" style="font-size: 0.65rem; letter-spacing: 0.5px;">System</p>
        
        <ul class="nav nav-pills flex-column">
            {{-- Settings --}}
            <li class="nav-item mb-1">
                <a href="{{ route('settings') }}" class="nav-link text-white-50 {{ request()->routeIs('settings') ? 'active' : '' }}">
                    <i class="bi bi-gear me-3"></i>
                    <span>Settings</span>
                </a>
            </li>

            {{-- Help --}}
            <li class="nav-item mb-1">
                <a href="{{ route('help') }}" class="nav-link text-white-50 {{ request()->routeIs('help') ? 'active' : '' }}">
                    <i class="bi bi-question-circle me-3"></i>
                    <span>Help & Support</span>
                </a>
            </li>
        </ul>
    </div>

    {{-- User Profile Section at Bottom --}}
    <div class="sidebar-footer border-top border-secondary border-opacity-25 p-3">
        <a href="{{ route('profile.edit') }}" class="d-flex align-items-center text-white text-decoration-none p-2 rounded-3 hover-bg-light">
            <div class="position-relative">
                <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name ?? 'User' }}&background=0D6EFD&color=fff&size=50&bold=true" 
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