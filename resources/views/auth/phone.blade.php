@extends('layouts.auth')

@section('content')
<div class="position-relative overflow-hidden radial-gradient min-vh-100 w-100 d-flex align-items-center justify-content-center">
    <div class="d-flex align-items-center justify-content-center w-100">
        <div class="row justify-content-center w-100">
            <div class="col-md-8 col-lg-6 col-xxl-3 auth-card">
                <div class="card mb-0 rounded-4">
                    <div class="card-body">
                        <a href="#" class="text-nowrap logo-img text-center d-block w-100">
                            <img src="{{filePath(setting('site.logo'))}}" class="" alt="Logo App" style="height:4rem" />
                        </a>
                        {{-- <div class="position-relative text-center my-4">
                            <p class="mb-0 fs-4 px-3 d-inline-block bg-body text-dark z-index-5 position-relative">sign in with</p>
                            <span class="border-top w-100 position-absolute top-50 start-50 translate-middle"></span>
                        </div> --}}
                        <form method="POST" action="{{ route('login.phone') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="phone" class="form-label">Whatsapp Number</label>
                                <input type="number" name="phone" class="form-control @error('phone') is-invalid @enderror" id="phone" aria-describedby="phoneHelp" value="{{ old('phone') }}" required autocomplete="phone" autofocus>
                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-8 mb-2 rounded-2">Kirim OTP</button>
                            <div class="d-flex align-items-center gap-2 text-center">
                                <hr class="" style="width:100%; height:2px;">
                                <div class="fs-2 text-muted" style="white-space: nowrap">Atau coba masuk dengan</div>
                                <hr class="" style="width:100%; height:2px;">
                            </div>
                            <div class="d-flex align-items-center justify-content-center mb-2 gap-2">
                                <a href="{{route('login')}}" class="btn btn-sm btn-outline-secondary rounded-2 p-2 px-3 d-flex align-items-center gap-2"><i class="ti ti-password fs-5"></i> Password</a>
                                <a href="{{route('login.email')}}" class="btn btn-sm btn-outline-danger rounded-2 p-2 px-3 d-flex align-items-center gap-2"><i class="ti ti-mail fs-5"></i> Email</a>
                            </div>
                            <div class="d-flex align-items-center gap-2 text-center">
                                <hr class="" style="width:100%; height:2px;">
                                <div class="fs-2 text-muted" style="white-space: nowrap">Belum punya akun ?</div>
                                <hr class="" style="width:100%; height:2px;">
                            </div>
                            <a href="" class="btn btn-light w-100 py-8 mb-3 rounded-2">Hubungi Admin</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
