<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Laravel Admin')</title>
    
    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    {{-- Custom Styles --}}
    @stack('styles')
    
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .main-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        .main-content {
            flex: 1;
            min-width: 0;
        }
        
        .sidebar .nav-link.active {
            background: linear-gradient(135deg, #0d6efd, #0a58ca) !important;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }
        
        .sidebar .nav-link {
            border-radius: 10px;
            padding: 0.7rem 1rem;
            transition: all 0.2s ease;
        }
        
        .sidebar .nav-link:hover:not(.active) {
            background: rgba(255, 255, 255, 0.08);
            color: #fff !important;
            transform: translateX(4px);
        }
        
        .sidebar .nav-link .bi-chevron-down {
            transition: transform 0.3s ease;
        }
        
        .sidebar .nav-link[aria-expanded="true"] .bi-chevron-down {
            transform: rotate(180deg);
        }
        
        .sidebar .collapse .nav-link {
            padding: 0.4rem 1rem;
            font-size: 0.9rem;
        }
        
        .sidebar .collapse .nav-link.active {
            background: rgba(13, 110, 253, 0.2) !important;
            color: #0d6efd !important;
            box-shadow: none;
        }
        
        .sidebar-brand .brand-icon {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 2px;
        }
        
        .badge {
            font-weight: 500;
        }
        
        /* Glassmorphism effect for cards */
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body>
<div class="main-wrapper">
    {{-- Desktop Sidebar --}}
    <div class="d-none d-lg-block">
        <x-sidebar />
    </div>

    {{-- Main Content Area --}}
    <div class="main-content">
        {{-- Toast Messages - Should be at the very top --}}
        @include('messages')

        {{-- Header --}}
        <x-header />

        {{-- Page Content --}}
        <div class="p-4">
            @yield('content')
        </div>
    </div>
</div>

    {{-- Bootstrap JS Bundle --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- Additional Scripts --}}
    @stack('scripts')
    
    <script>
        // Theme Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('themeToggle');
            let darkMode = false;
            
            themeToggle.addEventListener('click', function() {
                darkMode = !darkMode;
                const icon = this.querySelector('i');
                if (darkMode) {
                    icon.className = 'bi bi-sun-fill fs-5';
                    document.body.style.backgroundColor = '#1a1a2e';
                    document.querySelector('.header').style.backgroundColor = 'rgba(26, 26, 46, 0.95)';
                    document.querySelector('.header').style.borderBottomColor = 'rgba(255,255,255,0.05)';
                    // Add dark mode styles
                } else {
                    icon.className = 'bi bi-moon-fill fs-5';
                    document.body.style.backgroundColor = '#f4f6f9';
                    document.querySelector('.header').style.backgroundColor = 'rgba(255, 255, 255, 0.95)';
                    document.querySelector('.header').style.borderBottomColor = '1px solid rgba(0,0,0,0.04)';
                }
            });
        });
    </script>
</body>
</html>