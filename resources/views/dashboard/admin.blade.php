@extends ('layouts.base')
@section('css')
    <link rel="stylesheet" href="/assets/libs/owl.carousel/dist/assets/owl.carousel.min.css" />
@endsection
@section('content')
    <section>
        <div class="card w-100 bg-primary-subtle overflow-hidden shadow-none">
            <div class="card-body position-relative">
                <div class="row">
                    <div class="col-sm-7">
                        <div class="d-flex align-items-center mb-7">
                            <div class="rounded-circle overflow-hidden me-6">
                                <img src="{{ auth()->user()->image ? '/storage/'.auth()->user()->image : '/assets/images/profile/user-1.jpg'}}" alt="" width="40"
                                    height="40" class="object-fit-cover">
                            </div>
                            <h5 class="fw-semibold mb-0 fs-5">Selamat Datang {{auth()->user()->name}}!</h5>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="border-end pe-4 border-muted border-opacity-10">
                                <h3 class="mb-1 fw-semibold fs-8 d-flex align-content-center">
                                    {{ $count->stage }}
                                </h3>
                                <p class="mb-0 text-dark">Total Stage</p>
                            </div>
                            <div class="ps-4">
                                <h3 class="mb-1 fw-semibold fs-8 d-flex align-content-center">
                                    {{ $count->task }}
                                </h3>
                                <p class="mb-0 text-dark">Total Task</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <div class="welcome-bg-img mb-n7 text-end">
                            <img src="/assets/images/backgrounds/welcome-bg.svg" alt="" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section>
        <h2 class="mb-4">Akses Aplikasi</h2>
        <!--  Owl carousel -->
        <div class="owl-carousel counter-carousel owl-theme">
        @php
            $classArrays = [
                'bg-primary-subtle text-primary',
                'bg-secondary-subtle',
                'bg-info-subtle',
            ];
        @endphp
        {{-- {{(string) $classArrays[$loop->index]}} --}}
            @forelse (auth()->user()->applications() as $app)
                @if($app && $app->code)
                    <div class="item">
                        <a href="{{urlApp($app?->code)}}" class="card zoom-in {{$app->url == url()->current() ? 'bg-danger-subtle shadow-md' : 'bg-primary-subtle text-primary shadow-none'}}" target="_blank" style="aspect-ratio:1/1 !important">
                            <div class="card-body">
                                <div class="text-center">
                                    @if ($app?->icon)
                                        <i class="ti ti-{{$app?->icon}} fs-12"></i>
                                    @else
                                        <img src="{{$app?->image ? filePath($app->image) : filePath(setting('site.logo'))}}" width="50" height="50" class="mb-3 object-fit-contain"
                                        alt="" />
                                    @endif
                                    <p class="fw-semibold fs-5 mt-4 mb-1">
                                        {{$app?->name}}
                                    </p>
                                    <div class="fw-semibold fs-2 line-clamp line-clamp-2 text-muted mb-0">{{$app?->url}}</div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
            @empty
            <div class="p-5 rounded-4 border border-dashed text-center">Tidak ada akses aplikasi</div>
            @endforelse
        </div>
    </section>
@endsection

@section('scripts')
    <script src="/assets/libs/owl.carousel/dist/owl.carousel.min.js"></script>
    <script src="/assets/libs/apexcharts/dist/apexcharts.min.js"></script>

    <script>
        $(function() {
            $(".counter-carousel").owlCarousel({
                loop: true,
                rtl: true,
                margin: 30,
                mouseDrag: true,
                autoplay: true,
                autoplayDuration: 2000,
                autoplayHoverPause: true,
                dots: false,
                nav: false,

                responsive: {
                    0: {
                        items: 2,
                        loop: true,
                    },
                    576: {
                        items: 2,
                        loop: true,
                    },
                    768: {
                        items: 3,
                        loop: true,
                    },
                    1200: {
                        items: 5,
                        loop: true,
                    },
                    1400: {
                        items: 6,
                        loop: true,
                    },
                },
            });
        })
    </script>
@endsection
