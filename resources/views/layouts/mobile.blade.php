<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr" data-bs-theme="light"
    data-color-theme="Blue_Theme">

<head>
    <!-- Required meta tags -->
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Tanur Muthmainnah') }}</title>

    <!-- Favicon icon-->
    <link rel="shortcut icon" type="image/png" href="/storage/{{setting('site.favicon')}}" />

    <!-- Core Css -->
    <link rel="stylesheet" href="/assets/css/styles.css" />
    <link rel="stylesheet" href="/css/app.css" />
    <link rel="stylesheet" href="{{url('/app.css')}}" />
    
    <style>
        /* Wrapper agar tetap terpusat */
        .device-wrapper {
            display: flex;
            justify-content: center;
            padding: 2rem 1rem;
        }

        /* Frame HP */
        .device-frame {
            max-width: 390px;
            width: 100%;
            border: 16px solid #333;
            border-radius: 36px;
            background: #000;
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.2);
            position: relative;
        }

        /* Notch */
        .device-frame::before {
            content: '';
            position: absolute;
            top: -6px;
            left: 50%;
            transform: translateX(-50%);
            width: 120px;
            height: 20px;
            background: #333;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        .device-screen {
            background: #fff;
            border-radius: 20px;
            min-height: 700px;
            max-height: 700px;
            position: relative;
            overflow: hidden; /* hindari scroll seluruh screen */
            display: flex;
            padding-bottom: 2em;
            flex-direction: column;
        }

        .device-screen > .h-100 {
            flex: 1;
            overflow-y: auto; /* scroll hanya di konten, bukan seluruh layar */
            padding-bottom: 80px; /* beri jarak agar tidak tertutup menu */

            /* Scrollbar custom */
            scrollbar-width: thin;              /* Firefox */
            scrollbar-color: #ccc transparent;  /* Firefox */
        }
        
        .device-screen > .h-100::-webkit-scrollbar {
            width: 4px; /* 👈 Ini mengatur lebar scrollbar */
        }

        .device-screen > .h-100::-webkit-scrollbar-track {
            background: transparent; /* 👈 Track transparan */
        }

        .device-screen > .h-100::-webkit-scrollbar-thumb {
            background-color: #bbb;  /* Warna scrollbar-nya */
            border-radius: 4px;
        }

        /* Mobile Menu tetap di bawah layar perangkat */
        .mobile-menu {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 9999;
        }

        /* Responsive: hilangkan frame saat layar kecil */
        @media (max-width: 576px) {
            .device-wrapper {
                padding: 0;
            }

            .device-frame {
                max-width: 100%;
                border: none;
                border-radius: 0;
                box-shadow: none;
                background: transparent;
            }

            .device-frame::before {
                display: none;
            }

            .device-screen {
                border-radius: 0;
                min-height: 100vh;
            }

            .device-screen > .h-100 {
                padding-bottom: 80px;
            }

            .mobile-menu {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
            }
        }
    </style>
    @yield('css')
</head>

<body class="bg-black">
    <!-- Preloader -->
    <div class="preloader">
        <img src="/assets/images/logos/favicon.png" alt="loader" class="lds-ripple img-fluid" />
    </div>
    <div class="device-wrapper">
        <div class="device-frame">
            <div class="device-screen position-relative">
                
                <div class="h-100">
                    @yield('content')
                </div>

                {{-- Mobile Menu --}}
                <div class="mobile-menu p-3">
                    <div class="d-flex align-items-center justify-content-around gap-3 bg-white p-2 rounded-3 shadow-sm">
                        <a href="{{route('agent.approval.index', request()->route('id'))}}" style="width: 50px" class="text-dark d-flex align-items-center justify-content-center flex-column">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M9.615 20H7C6.46957 20 5.96086 19.7893 5.58579 19.4142C5.21071 19.0391 5 18.5304 5 18V6C5 5.46957 5.21071 4.96086 5.58579 4.58579C5.96086 4.21071 6.46957 4 7 4H15C15.5304 4 16.0391 4.21071 16.4142 4.58579C16.7893 4.96086 17 5.46957 17 6V14M14 19L16 21L20 17M9 8H13M9 12H11"
                                        stroke="{{Route::is('agent.approval.index') ? '#027673' : 'currentColor'}}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="{{Route::is('agent.approval.index') ? 'd-block' : 'd-none'}} fs-1">Approval</div>
                        </a>
                        <a href="{{route('agent.history.index', request()->route('id'))}}" style="width: 50px" class="text-dark d-flex align-items-center justify-content-center flex-column">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M10 20.7769C9.12989 20.5786 8.29407 20.2521 7.52002 19.8079M14 3.2229C15.9882 3.67697 17.7633 4.79259 19.0347 6.38711C20.3061 7.98162 20.9985 9.96055 20.9985 11.9999C20.9985 14.0392 20.3061 16.0182 19.0347 17.6127C17.7633 19.2072 15.9882 20.3228 14 20.7769M4.57902 17.0929C4.03412 16.3 3.61986 15.4249 3.35202 14.5009M3.12402 10.4999C3.28402 9.5499 3.59202 8.6499 4.02402 7.8249L4.19302 7.5199M6.90702 4.5789C7.84322 3.93578 8.8927 3.47568 10 3.2229"
                                        stroke="{{Route::is('agent.history.index') ? '#027673' : 'currentColor'}}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 8V12L15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="{{Route::is('agent.history.index') ? 'd-block' : 'd-none'}} fs-1">History</div>
                        </a>
                        <div class="d-flex justify-content-center">
                            {{-- Logo Mobile --}}
                            <a href="{{route('agent.workspace.index', request()->route('id'))}}" class="d-flex align-items-center justify-content-center" 
                                style="border-radius: 20px;
                                        background: linear-gradient(58deg, #9D844A 0%, #3D7A63 37.18%, #0C4961 67.77%, #133258 115.54%);
                                        width: 60px;
                                        height: 60px;
                                        margin-top: -2em;
                            ">
                                <img src="{{'/storage/'.setting('site.mobile-logo')}}" alt="Logo WPS Menu Mobile" class="d-block" style="width: 100%; padding:10px">
                            </a>
                        </div>
                        <a href="{{route('agent.workspace.list', request()->route('id'))}}" style="width: 50px" class="text-dark d-flex align-items-center justify-content-center flex-column">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M17 7C17 6.20435 16.6839 5.44129 16.1213 4.87868C15.5587 4.31607 14.7956 4 14 4H10C9.20435 4 8.44129 4.31607 7.87868 4.87868C7.31607 5.44129 7 6.20435 7 7H6C5.20435 7 4.44129 7.31607 3.87868 7.87868C3.31607 8.44129 3 9.20435 3 10V18C3 18.7956 3.31607 19.5587 3.87868 20.1213C4.44129 20.6839 5.20435 21 6 21H18C18.7956 21 19.5587 20.6839 20.1213 20.1213C20.6839 19.5587 21 18.7956 21 18V10C21 9.20435 20.6839 8.44129 20.1213 7.87868C19.5587 7.31607 18.7956 7 18 7H17ZM14 6H10C9.73478 6 9.48043 6.10536 9.29289 6.29289C9.10536 6.48043 9 6.73478 9 7H15C15 6.73478 14.8946 6.48043 14.7071 6.29289C14.5196 6.10536 14.2652 6 14 6ZM6 9H18C18.2652 9 18.5196 9.10536 18.7071 9.29289C18.8946 9.48043 19 9.73478 19 10V18C19 18.2652 18.8946 18.5196 18.7071 18.7071C18.5196 18.8946 18.2652 19 18 19H6C5.73478 19 5.48043 18.8946 5.29289 18.7071C5.10536 18.5196 5 18.2652 5 18V10C5 9.73478 5.10536 9.48043 5.29289 9.29289C5.48043 9.10536 5.73478 9 6 9Z" 
                                        fill="{{Route::is('agent.workspace.list') ? '#027673' : 'currentColor'}}"/>
                                </svg>
                            </div>
                            <div class="{{Route::is('agent.workspace.list') ? 'd-block' : 'd-none'}} fs-1">Workspace</div>
                        </a>
                        <a href="{{route('agent.workspace.add', request()->route('id'))}}" style="width: 50px" class="text-dark d-flex align-items-center justify-content-center flex-column">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 34 34" fill="none">
                                    <path d="M17.0002 2.83325L17.4592 2.83467L17.9097 2.84034L18.7823 2.86442L19.2059 2.88284L20.0262 2.931L20.8096 2.99617C27.5883 3.6535 30.3466 6.41175 31.0039 13.1905L31.0691 13.9739L31.1172 14.7942C31.1248 14.933 31.1309 15.0742 31.1357 15.2178L31.1597 16.0904L31.1668 16.9999L31.1597 17.9094L31.1357 18.7821L31.1172 19.2057L31.0691 20.0259L31.0039 20.8093C30.3466 27.5881 27.5883 30.3463 20.8096 31.0037L20.0262 31.0688L19.2059 31.117C19.0671 31.1246 18.9259 31.1307 18.7823 31.1354L17.9097 31.1595L17.0002 31.1666L16.0907 31.1595L15.218 31.1354L14.7944 31.117L13.9742 31.0688L13.1907 31.0037C6.412 30.3463 3.65375 27.5881 2.99641 20.8093L2.93125 20.0259L2.88308 19.2057L2.86466 18.7821L2.84058 17.9094C2.83586 17.6119 2.8335 17.3088 2.8335 16.9999L2.83491 16.5409L2.84058 16.0904L2.86466 15.2178L2.88308 14.7942L2.93125 13.9739L2.99641 13.1905C3.65375 6.41175 6.412 3.6535 13.1907 2.99617L13.9742 2.931L14.7944 2.88284C14.9332 2.87528 15.0744 2.86914 15.218 2.86442L16.0907 2.84034C16.3882 2.83561 16.6913 2.83325 17.0002 2.83325ZM17.0002 11.3333C16.6244 11.3333 16.2641 11.4825 15.9984 11.7482C15.7328 12.0139 15.5835 12.3742 15.5835 12.7499V15.5833H12.7502L12.5844 15.5932C12.2259 15.6358 11.8971 15.8138 11.6654 16.0907C11.4336 16.3675 11.3163 16.7225 11.3375 17.0829C11.3587 17.4434 11.5167 17.7822 11.7792 18.03C12.0418 18.2779 12.3891 18.4162 12.7502 18.4166H15.5835V21.2499L15.5934 21.4157C15.6361 21.7742 15.814 22.103 16.0909 22.3347C16.3678 22.5665 16.7227 22.6837 17.0832 22.6626C17.4436 22.6414 17.7824 22.4834 18.0303 22.2209C18.2782 21.9583 18.4164 21.611 18.4168 21.2499V18.4166H21.2502L21.4159 18.4067C21.7745 18.364 22.1032 18.1861 22.335 17.9092C22.5667 17.6323 22.684 17.2774 22.6628 16.9169C22.6417 16.5564 22.4837 16.2177 22.2211 15.9698C21.9585 15.7219 21.6112 15.5837 21.2502 15.5833H18.4168V12.7499L18.4069 12.5842C18.3663 12.2396 18.2007 11.9218 17.9414 11.6913C17.6821 11.4607 17.3471 11.3333 17.0002 11.3333Z"
                                        fill=" {{Route::is('agent.workspace.add') ? '#027673' : 'currentColor'}}"/>
                                </svg>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Alerting Session --}}
    @if (session('success'))
        <div class="modal fade" id="al-success-alert" tabindex="-1" aria-labelledby="vertical-center-modal"
            aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content modal-filled bg-success-subtle text-success">
                    <div class="modal-body p-4">
                        <div class="text-center text-success">
                            <i class="ti ti-circle-check fs-7"></i>
                            <h4 class="mt-2">Well Done!</h4>
                            <p class="mt-3 text-success-50">{{ session('success') }}</p>
                            <button type="button" class="btn btn-light my-2"
                                data-bs-dismiss="modal">Continue</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var successModal = new bootstrap.Modal(document.getElementById('al-success-alert'));
                successModal.show();
            });
        </script>
    @endif

    @if (session('error'))
        <div class="modal fade" id="al-danger-alert" tabindex="-1" aria-labelledby="vertical-center-modal"
            aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content modal-filled bg-danger-subtle">
                    <div class="modal-body p-4">
                        <div class="text-center text-danger">
                            <i class="ti ti-hexagon-letter-x fs-7"></i>
                            <h4 class="mt-2">Oh snap!</h4>
                            <p class="mt-3">{{ session('error') }}</p>
                            <button type="button" class="btn btn-light my-2"
                                data-bs-dismiss="modal">Continue</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var errorModal = new bootstrap.Modal(document.getElementById('al-danger-alert'));
                errorModal.show();
            });
        </script>
    @endif

    <div class="modal fade" id="error-alert" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content modal-filled bg-danger-subtle text-danger">
                <div class="modal-body p-4">
                    <div class="text-center text-danger">
                        <i class="ti ti-alert-circle fs-7"></i>
                        <h4 class="mt-2">Peringatan!</h4>
                        <p class="mt-3 text-danger-50" id="error-alert-message">Terjadi kesalahan.</p>
                        <button type="button" class="btn btn-light my-2" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/vendor.min.js"></script>
    <!-- Import Js Files -->
    <script src="/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/libs/simplebar/dist/simplebar.min.js"></script>
    <script src="/assets/js/theme/app.init.js"></script>
    <script src="/assets/js/theme/theme.js"></script>
    <script src="/assets/js/theme/app.min.js"></script>

    <!-- solar icons -->
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
    <script>
        $(document).ready(function () {
        // Open modal
        $(document).on('click', '.btn-add-modal', function () {
            let modalId = $(this).data('modal-id');
            $(`#${modalId}`).slideToggle(300);
            $('.mobile-menu').hide();
        });

        // Close modal
        $(document).on('click', '.btn-close-modal', function () {
            let modalId = $(this).data('modal-id');
            $(`#${modalId}`).slideToggle(300);
            $('.mobile-menu').show();
        });
    });
    </script>
    @yield('scripts')
</body>

</html>
