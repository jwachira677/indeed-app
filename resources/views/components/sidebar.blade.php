<!-- resources/views/components/sidebar.blade.php -->
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

    {{-- Sidebar Navigation --}}
    <div class="sidebar-nav flex-grow-1 overflow-auto px-3 py-4">

        <ul class="nav nav-pills flex-column mb-4">
            
            {{-- 1. Main Menu --}}
            <li class="nav-item mb-1">
                <a class="nav-link text-white-50 d-flex align-items-center collapsed" data-bs-toggle="collapse" href="#mainMenu" role="button" aria-expanded="false" aria-controls="mainMenu">
                    <i class="bi bi-grid me-3"></i>
                    <span>Main Menu</span>
                    <i class="bi bi-chevron-down ms-auto transition-transform"></i>
                </a>
                <div class="collapse {{ request()->routeIs('dashboard', 'analytics') ? 'show' : '' }}" id="mainMenu">
                    <ul class="nav nav-pills flex-column ms-4 mt-1">
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link py-2 text-white-50 {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="bi bi-speedometer2 me-2 text-primary"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('analytics') }}" class="nav-link py-2 text-white-50 {{ request()->routeIs('analytics') ? 'active' : '' }}">
                                <i class="bi bi-bar-chart-line me-2 text-primary"></i> Analytics
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- 2. Students & Academic --}}
            <li class="nav-item mb-1">
                <a class="nav-link text-white-50 d-flex align-items-center collapsed" data-bs-toggle="collapse" href="#academicSection" role="button" aria-expanded="false" aria-controls="academicSection">
                    <i class="bi bi-person-video3 me-3"></i>
                    <span>Students &amp; Academic</span>
                    <i class="bi bi-chevron-down ms-auto transition-transform"></i>
                </a>
                <div class="collapse {{ request()->routeIs('students.*', 'enrollments.*', 'classes.*', 'teachers.*', 'academic-years.*', 'terms.*') ? 'show' : '' }}" id="academicSection">
                    <ul class="nav nav-pills flex-column ms-4 mt-1">
                        <li class="nav-item">
                            <a href="#" class="nav-link py-2 text-white-50 disabled-link" title="Coming soon">
                                <i class="bi bi-person me-2 text-primary"></i> Students
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link py-2 text-white-50 disabled-link" title="Coming soon">
                                <i class="bi bi-clipboard-check me-2 text-primary"></i> Enrollments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link py-2 text-white-50 disabled-link" title="Coming soon">
                                <i class="bi bi-door-open me-2 text-primary"></i> Classes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('teachers.index') }}" class="nav-link py-2 text-white-50 {{ request()->routeIs('teachers.*') ? 'active' : '' }}">
                                <i class="bi bi-person-workspace me-2 text-primary"></i> Teachers
                            </a>
                        </li>
                        {{-- Inner Dropdown: Academic Calendar --}}
                        <li class="nav-item">
                            <a class="nav-link py-2 text-white-50 collapsed d-flex align-items-center" data-bs-toggle="collapse" href="#academicMenu" role="button" aria-expanded="false" aria-controls="academicMenu">
                                <i class="bi bi-calendar3 me-2 text-primary"></i>
                                <span>Academic Calendar</span>
                                <i class="bi bi-chevron-down ms-auto" style="font-size: 0.75rem;"></i>
                            </a>
                            <div class="collapse" id="academicMenu">
                                <ul class="nav nav-pills flex-column ms-3 mt-1">
                                    <li class="nav-item">
                                        <a href="#" class="nav-link py-1 text-white-50 disabled-link">
                                            <i class="bi bi-calendar-year me-2 text-primary"></i> Academic Years
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link py-1 text-white-50 disabled-link">
                                            <i class="bi bi-calendar-week me-2 text-primary"></i> Terms
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- 3. CBC Curriculum --}}
            <li class="nav-item mb-1">
                <a class="nav-link text-white-50 d-flex align-items-center collapsed" data-bs-toggle="collapse" href="#cbcSection" role="button" aria-expanded="false" aria-controls="cbcSection">
                    <i class="bi bi-journal-bookmark me-3"></i>
                    <span>CBC Curriculum</span>
                    <i class="bi bi-chevron-down ms-auto transition-transform"></i>
                </a>
                <div class="collapse {{ request()->routeIs('grades.*', 'subjects.*', 'pathways.*', 'curriculum.*') ? 'show' : '' }}" id="cbcSection">
                    <ul class="nav nav-pills flex-column ms-4 mt-1">
                        <li class="nav-item">
                            <a href="#" class="nav-link py-2 text-white-50 disabled-link" title="Coming soon">
                                <i class="bi bi-layers me-2 text-primary"></i> Grade Levels
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link py-2 text-white-50 disabled-link" title="Coming soon">
                                <i class="bi bi-book me-2 text-primary"></i> Subjects
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link py-2 text-white-50 disabled-link" title="Coming soon">
                                <i class="bi bi-signpost-split me-2 text-primary"></i> Pathways
                            </a>
                        </li>
                        {{-- Inner Dropdown: Curriculum Mapping --}}
                        <li class="nav-item">
                            <a class="nav-link py-2 text-white-50 collapsed d-flex align-items-center" data-bs-toggle="collapse" href="#curriculumMenu" role="button" aria-expanded="false" aria-controls="curriculumMenu">
                                <i class="bi bi-diagram-3 me-2 text-primary"></i>
                                <span>Curriculum Mapping</span>
                                <i class="bi bi-chevron-down ms-auto" style="font-size: 0.75rem;"></i>
                            </a>
                            <div class="collapse" id="curriculumMenu">
                                <ul class="nav nav-pills flex-column ms-3 mt-1">
                                    <li class="nav-item">
                                        <a href="#" class="nav-link py-1 text-white-50 disabled-link">
                                            <i class="bi bi-list-check me-2 text-primary"></i> Grade Subjects
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link py-1 text-white-50 disabled-link">
                                            <i class="bi bi-shuffle me-2 text-primary"></i> Pathway Subjects
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link py-1 text-white-50 disabled-link">
                                            <i class="bi bi-person-check me-2 text-primary"></i> Student Subjects
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- 4. Fees & Finance --}}
            <li class="nav-item mb-1">
                <a class="nav-link text-white-50 d-flex align-items-center collapsed" data-bs-toggle="collapse" href="#financeSection" role="button" aria-expanded="false" aria-controls="financeSection">
                    <i class="bi bi-cash-stack me-3"></i>
                    <span>Fees &amp; Finance</span>
                    <i class="bi bi-chevron-down ms-auto transition-transform"></i>
                </a>
                <div class="collapse {{ request()->routeIs('fees.*', 'payments.*', 'bills.*') ? 'show' : '' }}" id="financeSection">
                    <ul class="nav nav-pills flex-column ms-4 mt-1">
                        <li class="nav-item">
                            <a href="#" class="nav-link py-2 text-white-50 disabled-link" title="Coming soon">
                                <i class="bi bi-tags me-2 text-primary"></i> Fee Types
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link py-2 text-white-50 disabled-link" title="Coming soon">
                                <i class="bi bi-table me-2 text-primary"></i> Fee Structures
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link py-2 text-white-50 disabled-link" title="Coming soon">
                                <i class="bi bi-receipt me-2 text-primary"></i> Student Bills
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link py-2 text-white-50 disabled-link" title="Coming soon">
                                <i class="bi bi-cash-coin me-2 text-primary"></i> Payments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link py-2 text-white-50 disabled-link" title="Coming soon">
                                <i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i> Finance Reports
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- 5. System --}}
            <li class="nav-item mb-1">
                <a class="nav-link text-white-50 d-flex align-items-center collapsed" data-bs-toggle="collapse" href="#systemSection" role="button" aria-expanded="false" aria-controls="systemSection">
                    <i class="bi bi-gear me-3"></i>
                    <span>System</span>
                    <i class="bi bi-chevron-down ms-auto transition-transform"></i>
                </a>
                <div class="collapse {{ request()->routeIs('users.*', 'roles.*', 'permissions.*', 'settings.*') ? 'show' : '' }}" id="systemSection">
                    <ul class="nav nav-pills flex-column ms-4 mt-1">
                        <li class="nav-item">
                            <a href="{{ route('users.index') }}" class="nav-link py-2 text-white-50 {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                <i class="bi bi-people me-2 text-primary"></i> Users
                                <span class="badge bg-primary bg-opacity-25 text-primary rounded-pill ms-auto">
                                    {{ \App\Models\User::count() }}
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('roles.index') }}" class="nav-link py-2 text-white-50 {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                <i class="bi bi-shield-lock me-2 text-primary"></i> Roles
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('permissions') }}" class="nav-link py-2 text-white-50 {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                                <i class="bi bi-key me-2 text-primary"></i> Permissions
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link py-2 text-white-50 disabled-link" title="Coming soon">
                                <i class="bi bi-sliders me-2 text-primary"></i> Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link py-2 text-white-50 disabled-link" title="Coming soon">
                                <i class="bi bi-question-circle me-2 text-primary"></i> Help &amp; Support
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

        </ul>
    </div>

    {{-- User Profile Section at Bottom --}}
    <div class="sidebar-footer border-top border-secondary border-opacity-25 p-3">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none p-2 rounded-3 hover-bg-light disabled-link">
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

{{-- Small style block for disabled links and animations --}}
<style>
    .disabled-link { cursor: not-allowed; opacity: 0.55; }
    .disabled-link:hover { background: transparent !important; }
    .nav-link.active { background: rgba(13, 110, 253, 0.15) !important; color: #fff !important; font-weight: 500; }
    .nav-link:hover:not(.disabled-link) { background: rgba(255,255,255,0.05); }
    .collapse .nav-link { font-size: 0.875rem; }
    .transition-transform { transition: transform 0.2s; }
    
    /* Rotate the chevron when the parent menu is open */
    [aria-expanded="true"] .bi-chevron-down { transform: rotate(180deg); }
    
    /* Subtle adjustments for the nested lists */
    .collapse ul .nav-link { padding-left: 10px; }
</style>