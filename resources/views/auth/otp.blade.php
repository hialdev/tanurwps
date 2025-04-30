@extends('layouts.auth')
@section('content')
    <div
        class="position-relative overflow-hidden radial-gradient min-vh-100 w-100 d-flex align-items-center justify-content-center">
        <div class="d-flex align-items-center justify-content-center w-100">
            <div class="row justify-content-center w-100">
                <div class="col-md-8 col-lg-6 col-xxl-3 auth-card">
                    <div class="card mb-0">
                        <div class="card-body pt-5">
                            <a href="../main/index.html" class="text-nowrap logo-img text-center d-block mb-4 w-100">
                                <img src="{{filePath(setting('site.logo'))}}" class="" alt="Logo App" style="height:4rem" />
                            </a>
                            <div class="mb-5 text-center">
                                <p>Kami telah mengirimkan kode OTP ke {{request()->get('type')}}. Masukan kode OTP yang diterima oleh {{request()->get('type')}} dibawah ini.</p>
                                <h6 class="fw-bolder">
                                    {{ hideData(Illuminate\Support\Facades\Crypt::decrypt(request()->get('data'))) }}
                                </h6>
                            </div>
                            <form method="POST" action="{{route('otp.submit', request()->get('type'))}}">
                                @csrf
                                <input type="hidden" name="data" value="{{Illuminate\Support\Facades\Crypt::decrypt(request()->get('data'))}}">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label fw-semibold">Masukan Kode 6 digit</label>
                                    <div class="d-flex align-items-center gap-2 gap-sm-3">
                                        @for ($i = 1; $i <= 6; $i++)
                                            <input type="text" class="form-control otp-input text-center" maxlength="1" name="otp[]" data-index="{{ $i }}" autocomplete="off">
                                        @endfor
                                    </div>
                                </div>
                                <button href="javascript:void(0)" class="btn btn-primary w-100 py-8 mb-4">Submit</button>
                            </form>
                            <div class="d-flex align-items-center">
                                <p class="fs-4 mb-0 text-dark">Tidak menerima kode?</p>
                                <form method="POST" action="{{route('login.'.request()->get('type'))}}">
                                @csrf
                                    <input type="hidden" name="{{request()->get('type')}}" value="{{Illuminate\Support\Facades\Crypt::decrypt(request()->get('data'))}}">
                                    <button class="btn btn-sm btn-light text-primary fw-medium ms-2">Kirim Ulang</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const inputs = document.querySelectorAll(".otp-input");

        inputs.forEach((input, index) => {
            input.addEventListener("input", function () {
                if (this.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus(); // Pindah ke input berikutnya
                }
            });

            input.addEventListener("keydown", function (e) {
                if (e.key === "Backspace" && this.value === "" && index > 0) {
                    inputs[index - 1].focus(); // Kembali ke input sebelumnya
                }
            });
        });
    });
</script>
@endsection
