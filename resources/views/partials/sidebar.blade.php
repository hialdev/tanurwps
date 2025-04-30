<!-- Sidebar Start -->
<aside class="left-sidebar with-vertical">
    <div><!-- ---------------------------------- -->
        <!-- Start Vertical Layout Sidebar -->
        <!-- ---------------------------------- -->
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="{{ route('home') }}" class="text-nowrap logo-img">
                <img src="/storage/{{setting('site.logo')}}" class="{{env('APP_NAME')}}" alt="{{env('APP_NAME')}} Logo in Sidebar"
                    style="height: 3em; width:auto; object-fit:contain;" />
            </a>
            <a href="javascript:void(0)" class="sidebartoggler ms-auto text-decoration-none fs-5 d-block d-xl-none">
                <i class="ti ti-x"></i>
            </a>
        </div>


        <nav class="sidebar-nav scroll-sidebar" data-simplebar>
            <ul id="sidebarnav">
                <!-- ---------------------------------- -->
                <!-- Home -->
                <!-- ---------------------------------- -->
                <li class="nav-small-cap mt-2">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Home</span>
                </li>
                <li class="sidebar-item {{ Route::is('home') ? 'selected' : '' }}">
                    <a href="{{ route('home') }}" class="sidebar-link" aria-expanded="false">
                        <span>
                            <i class="ti ti-home"></i>
                        </span>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>

                <!-- ---------------------------------- -->
                <!-- WPS Management -->
                <!-- ---------------------------------- -->
                <li class="nav-small-cap mt-0">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">WPS Management</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <span class="d-flex">
                            <i class="ti ti-timeline-event"></i>
                        </span>
                        <span class="hide-menu">Stages</span>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level">
                        <li class="sidebar-item">
                            <a href="{{ route('stage.index') }}" class="sidebar-link">
                                <div class="round-16 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-circle"></i>
                                </div>
                                <span class="hide-menu">Semua Stage</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('stage.add') }}" class="sidebar-link">
                                <div class="round-16 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-circle"></i>
                                </div>
                                <span class="hide-menu">Tambah</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <span class="d-flex">
                            <i class="ti ti-checklist"></i>
                        </span>
                        <span class="hide-menu">Tasks</span>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level">
                        <li class="sidebar-item">
                            <a href="{{ route('task.index') }}" class="sidebar-link">
                                <div class="round-16 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-circle"></i>
                                </div>
                                <span class="hide-menu">Semua Tasks</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('task.add') }}" class="sidebar-link">
                                <div class="round-16 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-circle"></i>
                                </div>
                                <span class="hide-menu">Tambah</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-item {{ Route::is('agent.*') ? 'selected' : '' }}">
                    <a href="{{ route('agent.index') }}" class="sidebar-link" aria-expanded="false">
                        <span>
                            <i class="ti ti-user-circle"></i>
                        </span>
                        <span class="hide-menu">Agents</span>
                    </a>
                </li>

                @role(['admin', 'developer'])
                    <!-- ---------------------------------- -->
                    <!-- Site Management -->
                    <!-- ---------------------------------- -->
                    <li class="nav-small-cap mt-0">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">Site Management</span>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                            <span class="d-flex">
                                <i class="ti ti-apps"></i>
                            </span>
                            <span class="hide-menu">Application</span>
                        </a>
                        <ul aria-expanded="false" class="collapse first-level">
                            <li class="sidebar-item">
                                <a href="{{ route('application.index') }}" class="sidebar-link">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Semua Aplikasi</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a href="{{ route('application.add') }}" class="sidebar-link">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Tambah</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                            <span class="d-flex">
                                <i class="ti ti-users"></i>
                            </span>
                            <span class="hide-menu">User</span>
                        </a>
                        <ul aria-expanded="false" class="collapse first-level">
                            <li class="sidebar-item">
                                <a href="{{ route('user.index') }}" class="sidebar-link">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Semua User</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a href="{{ route('user.add') }}" class="sidebar-link">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Tambah</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-item {{ Route::is('access.index') ? 'selected' : '' }}">
                        <a href="{{ route('access.index') }}" class="sidebar-link" aria-expanded="false">
                            <span>
                                <i class="ti ti-fingerprint"></i>
                            </span>
                            <span class="hide-menu">Access Control</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ Route::is('setting.index') ? 'selected' : '' }}">
                        <a href="{{ route('setting.index') }}" class="sidebar-link" aria-expanded="false">
                            <span>
                                <i class="ti ti-settings"></i>
                            </span>
                            <span class="hide-menu">Settings</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ Route::is('whatsapp.index') ? 'selected' : '' }}">
                        <a href="{{ route('whatsapp.index') }}" class="sidebar-link" aria-expanded="false">
                            <span>
                                <i class="ti ti-brand-whatsapp"></i>
                            </span>
                            <span class="hide-menu">Whatsapp OTP</span>
                        </a>
                    </li>
                @endrole
            </ul>
        </nav>
        <div class="fixed-profile p-3 mx-4 mb-2 bg-secondary-subtle rounded mt-3">
            <div class="hstack gap-3">
                <div class="john-img">
                    <img src="{{ auth()->user() && auth()->user()->image ? '/storage/'. auth()->user()->image : '/assets/images/profile/user-1.jpg' }}"
                        class="rounded-circle" width="40" height="40" style="object-fit: cover"
                        alt="Image User {{ auth()->user() ? auth()->user()->name : 'Pengunjung' }}" />
                </div>
                <div class="john-title">
                    <h6 class="mb-0 fs-2 fw-semibold">{{ auth()->user() ? auth()->user()->name : 'Pengunjung' }}</h6>
                    <span class="fs-2">{{ auth()->user() ? auth()->user()->getRoleNames()[0] : 'Masuk Dahulu' }}</span>
                </div>
                @if(auth()->user())
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="border-0 bg-transparent text-primary ms-auto" tabindex="0"
                        aria-label="logout" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Keluar">
                        <i class="ti ti-power fs-6"></i>
                    </button>
                </form>
                @else
                    <a href="{{route('login')}}" class="border-0 bg-transparent text-primary ms-auto" tabindex="0"
                        aria-label="logout" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Masuk">
                        <i class="ti ti-login fs-6"></i>
                    </a>
                @endif
            </div>
        </div>
        <!-- ---------------------------------- -->
        <!-- Start Vertical Layout Sidebar -->
        <!-- ---------------------------------- -->
    </div>
</aside>
<!--  Sidebar End -->
