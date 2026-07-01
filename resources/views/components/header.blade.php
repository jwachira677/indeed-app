<!-- resources/views/components/header.blade.php -->
<header class="header bg-white shadow-sm px-4 py-3 d-flex align-items-center justify-content-between" 
        style="height: 72px; border-bottom: 1px solid rgba(0,0,0,0.04); position: sticky; top: 0; z-index: 1030;">
    
    {{-- Left Side: Toggle & Search --}}
    <div class="d-flex align-items-center gap-3">
        {{-- Mobile Toggle --}}
        <button class="btn btn-link text-dark p-0 d-lg-none" 
                type="button" 
                data-bs-toggle="offcanvas" 
                data-bs-target="#sidebarOffcanvas">
            <i class="bi bi-list fs-2"></i>
        </button>

        {{-- Search Bar --}}
        <div class="search-wrapper position-relative d-none d-md-block">
            <i class="bi bi-search position-absolute text-secondary" 
               style="top: 50%; left: 16px; transform: translateY(-50%); font-size: 0.9rem;"></i>
            <input type="text" 
                   class="form-control bg-light border-0 rounded-pill ps-5" 
                   placeholder="Search for anything..." 
                   style="width: 320px; height: 42px; font-size: 0.9rem;">
            <kbd class="position-absolute text-secondary" 
                 style="top: 50%; right: 16px; transform: translateY(-50%); font-size: 0.65rem; background: #e9ecef; border: 1px solid #dee2e6;">
                ⌘K
            </kbd>
        </div>
    </div>

    {{-- Right Side: Actions --}}
    <div class="d-flex align-items-center gap-3">
        {{-- Theme Toggle --}}
        <button class="btn btn-link text-dark p-0" id="themeToggle" title="Toggle theme">
            <i class="bi bi-moon-fill fs-5"></i>
        </button>

        {{-- Notifications --}}
        <div class="dropdown">
            <button class="btn btn-link text-dark p-0 position-relative" 
                    type="button" 
                    data-bs-toggle="dropdown" 
                    data-bs-auto-close="outside"
                    aria-expanded="false">
                <i class="bi bi-bell fs-4"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                      style="font-size: 0.6rem; padding: 0.25rem 0.5rem; transform: translate(-30%, -30%);">
                    5
                    <span class="visually-hidden">unread notifications</span>
                </span>
            </button>
            
            {{-- Notification Dropdown --}}
            <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3 p-0" 
                 style="width: 380px; max-height: 500px; overflow-y: auto;">
                <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                    <h6 class="mb-0 fw-bold">Notifications</h6>
                    <button class="btn btn-link text-primary text-decoration-none p-0" style="font-size: 0.75rem;">
                        Mark all as read
                    </button>
                </div>
                
                <div class="notification-list">
                    {{-- Notification Item 1 --}}
                    <a href="#" class="dropdown-item d-flex align-items-start gap-3 p-3 border-bottom hover-bg-light">
                        <div class="notification-icon bg-primary bg-opacity-10 text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" 
                             style="width: 40px; height: 40px; flex-shrink: 0;">
                            <i class="bi bi-person-plus fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-1 small fw-semibold">New user registered</p>
                            <p class="mb-0 text-secondary small">Sarah Johnson created an account</p>
                            <span class="text-secondary" style="font-size: 0.65rem;">5 minutes ago</span>
                        </div>
                        <span class="badge bg-primary rounded-pill" style="width: 8px; height: 8px; padding: 0;"></span>
                    </a>

                    {{-- Notification Item 2 --}}
                    <a href="#" class="dropdown-item d-flex align-items-start gap-3 p-3 border-bottom hover-bg-light">
                        <div class="notification-icon bg-success bg-opacity-10 text-success rounded-circle p-2 d-flex align-items-center justify-content-center" 
                             style="width: 40px; height: 40px; flex-shrink: 0;">
                            <i class="bi bi-currency-dollar fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-1 small fw-semibold">Payment received</p>
                            <p class="mb-0 text-secondary small">$249.00 from Order #12345</p>
                            <span class="text-secondary" style="font-size: 0.65rem;">1 hour ago</span>
                        </div>
                        <span class="badge bg-primary rounded-pill" style="width: 8px; height: 8px; padding: 0;"></span>
                    </a>

                    {{-- Notification Item 3 --}}
                    <a href="#" class="dropdown-item d-flex align-items-start gap-3 p-3 border-bottom hover-bg-light">
                        <div class="notification-icon bg-warning bg-opacity-10 text-warning rounded-circle p-2 d-flex align-items-center justify-content-center" 
                             style="width: 40px; height: 40px; flex-shrink: 0;">
                            <i class="bi bi-exclamation-triangle fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-1 small fw-semibold">Stock alert</p>
                            <p class="mb-0 text-secondary small">3 products are running low</p>
                            <span class="text-secondary" style="font-size: 0.65rem;">3 hours ago</span>
                        </div>
                        <span class="badge bg-primary rounded-pill" style="width: 8px; height: 8px; padding: 0;"></span>
                    </a>

                    {{-- Notification Item 4 --}}
                    <a href="#" class="dropdown-item d-flex align-items-start gap-3 p-3 border-bottom hover-bg-light">
                        <div class="notification-icon bg-info bg-opacity-10 text-info rounded-circle p-2 d-flex align-items-center justify-content-center" 
                             style="width: 40px; height: 40px; flex-shrink: 0;">
                            <i class="bi bi-chat-dots fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-1 small fw-semibold">New comment</p>
                            <p class="mb-0 text-secondary small">John replied to your comment</p>
                            <span class="text-secondary" style="font-size: 0.65rem;">1 day ago</span>
                        </div>
                    </a>
                </div>

                <div class="p-2 text-center border-top">
                    <a href="#" class="text-decoration-none text-primary small fw-semibold">View all notifications</a>
                </div>
            </div>
        </div>

        {{-- Divider --}}
        <div class="vr text-secondary opacity-25" style="height: 30px;"></div>

        {{-- User Dropdown --}}
        <div class="dropdown">
            <button class="btn btn-link p-0 d-flex align-items-center gap-2 text-decoration-none" 
                    type="button" 
                    data-bs-toggle="dropdown" 
                    aria-expanded="false">
                <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name ?? 'User' }}&background=0D6EFD&color=fff&size=40&bold=true" 
                     alt="User" 
                     width="36" 
                     height="36" 
                     class="rounded-circle border border-2 border-primary border-opacity-25">
                <div class="d-none d-md-block text-start">
                    <div class="fw-semibold text-dark" style="font-size: 0.85rem;">
                        {{ auth()->user()->name ?? 'Guest User' }}
                    </div>
                    {{-- <span class="text-secondary" style="font-size: 0.7rem;">
                        {{ auth()->user()->role ?? 'Administrator' }}
                    </span> --}}
                </div>
                <i class="bi bi-chevron-down text-secondary ms-1" style="font-size: 0.7rem;"></i>
            </button>
            
            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3 py-2" style="min-width: 200px;">
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('profile.edit') }}">
                        <i class="bi bi-person fs-5 text-primary"></i>
                        <span>Profile</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="#">
                        <i class="bi bi-envelope fs-5 text-primary"></i>
                        <span>Messages</span>
                        <span class="badge bg-danger ms-auto">3</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="#">
                        <i class="bi bi-gear fs-5 text-primary"></i>
                        <span>Settings</span>
                    </a>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger">
                            <i class="bi bi-box-arrow-right fs-5"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

{{-- Offcanvas Sidebar for Mobile --}}
<div class="offcanvas offcanvas-start bg-dark" tabindex="-1" id="sidebarOffcanvas" style="width: 280px;">
    <div class="offcanvas-body p-0">
        <x-sidebar />
    </div>
</div>

@push('styles')
<style>
    .header {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
    }
    
    .hover-bg-light:hover {
        background-color: #f8f9fa;
    }
    
    .notification-icon {
        width: 40px;
        height: 40px;
        flex-shrink: 0;
    }
    
    .dropdown-menu {
        animation: slideDown 0.2s ease-out;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .notification-list::-webkit-scrollbar {
        width: 4px;
    }
    
    .notification-list::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    .notification-list::-webkit-scrollbar-thumb {
        background: #0d6efd;
        border-radius: 2px;
    }
</style>
@endpush