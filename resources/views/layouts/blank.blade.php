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
    @yield('css')
</head>

<body>
    <!-- Preloader -->
    <div class="preloader">
        <img src="/assets/images/logos/favicon.png" alt="loader" class="lds-ripple img-fluid" />
    </div>
    <div class="">
        <div class="container">
            @yield('content')
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

    <script src="/assets/js/vendor.min.js"></script>
    <!-- Import Js Files -->
    <script src="/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/libs/simplebar/dist/simplebar.min.js"></script>
    <script src="/assets/js/theme/app.init.js"></script>
    <script src="/assets/js/theme/theme.js"></script>
    <script src="/assets/js/theme/app.min.js"></script>

    <!-- solar icons -->
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

    @yield('scripts')
</body>

</html>
