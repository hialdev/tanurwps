<!--  Header Start -->
<header class="topbar">
    <div class="with-vertical">
        <!-- ---------------------------------- -->
        <!-- Start Vertical Layout Header -->
        <!-- ---------------------------------- -->
        <nav class="navbar navbar-expand-lg p-0">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link sidebartoggler nav-icon-hover ms-n3" id="headerCollapse" href="javascript:void(0)">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav quick-links d-none d-lg-flex">

            </ul>

            <div class="d-block ms-auto d-lg-none" style="width: fit-content">
                <a href="{{ route('home') }}" class="text-nowrap logo-img">
                    <img src="/storage/{{setting('site.logo')}}" class="" alt="{{env('APP_NAME')}} Logo" style="height: 3em" />
                </a>
            </div>
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-center">
                <!-- ------------------------------- -->
                <!-- start profile Dropdown -->
                <!-- ------------------------------- -->
                <li class="nav-item dropdown">
                    <a class="nav-link pe-0" href="javascript:void(0)" id="drop1" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <div class="user-profile-img">
                                <img src="{{ auth()->user() && auth()->user()->image ? '/storage/'. auth()->user()->image : '/assets/images/profile/user-1.jpg' }}"
                                    class="rounded-circle" width="35" height="35" style="object-fit: cover"
                                    alt="Image User of {{ auth()->user() ? auth()->user()->name : 'Pengujung Lagi' }}" />
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up"
                        aria-labelledby="drop1">
                        <div class="profile-dropdown position-relative" data-simplebar>
                            <div class="py-3 px-7 pb-0">
                                <h5 class="mb-0 fs-5 fw-semibold">User Profile</h5>
                            </div>
                            <div class="d-flex align-items-center py-9 mx-7 border-bottom">
                                <img src="{{ auth()->user() && auth()->user()->image ? '/storage/'. auth()->user()->image : '/assets/images/profile/user-1.jpg' }}"
                                    class="rounded-circle" width="80" height="80" style="object-fit: cover"
                                    alt="Again Image User of {{ auth()->user() ? auth()->user()->name : 'Pengunjung'}}" />
                                <div class="ms-3">
                                    <h5 class="mb-1 fs-3">{{ auth()->user() ? auth()->user()->name : 'Pengunjung' }}</h5>
                                    <span class="mb-1 d-block">{{ auth()->user() ? auth()->user()->getRoleNames()[0] : 'Masuk Dahulu' }}</span>
                                    @if(auth()->user())
                                    <p class="mb-0 d-flex align-items-center gap-2">
                                        <i class="ti ti-mail fs-4"></i> {{ auth()->user()->email}}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            <div class="message-body">
                                <a href="{{ urlApp('ACC', '/profile') }}" class="py-8 px-7 mt-8 d-flex align-items-center">
                                    <span
                                        class="d-flex align-items-center justify-content-center text-bg-light rounded-1 p-6">
                                        <img src="/assets/images/svgs/icon-account.svg" alt="" width="24"
                                            height="24" />
                                    </span>
                                    <div class="w-75 d-inline-block v-middle ps-3">
                                        <h6 class="mb-1 fs-3 fw-semibold lh-base">Profil</h6>
                                        <span class="fs-2 d-block text-body-secondary">Mengatur Data Profile / Akun</span>
                                    </div>
                                </a>
                            </div>
                            <div class="d-grid py-4 px-7 pt-8">
                                @if(auth()->user())
                                <form action="{{ route('logout') }}" method="POST" class="w-100">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger w-100">Keluar</button>
                                </form>
                                @else
                                <a href="{{route('login')}}" class="btn btn-primary w-100">Masuk</a>
                                @endif
                            </div>
                        </div>

                    </div>
                </li>
                <!-- ------------------------------- -->
                <!-- end profile Dropdown -->
                <!-- ------------------------------- -->
            </ul>
        </nav>
        <!-- ---------------------------------- -->
        <!-- End Vertical Layout Header -->
        <!-- ---------------------------------- -->

    </div>

</header>
<!--  Header End -->
