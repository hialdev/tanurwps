@extends('layouts.base')

@section('content')
    <div class="card bg-info-subtle shadow-none position-relative overflow-hidden mb-4">
        <div class="card-body px-4 py-3">
            <div class="row align-items-center">
                <div class="col-9">
                    <h4 class="fw-semibold mb-8">Whatsapp OTP</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a class="text-muted text-decoration-none" href="{{ route('home') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">whatsapp Connection</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-3">
                    <div class="text-center mb-n5">
                        <img src="/assets/images/breadcrumb/ChatBc.png" alt="" class="img-fluid mb-n4" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card" id="main-view">
        <div class="card-body pb-2">
            <div>
                <form method="post" id="form_security" autocomplete="off">
                    <div class="form-group row mb-3">
                        <div class="col-12">
                            <div class="form-control-plaintext">
                                In order to use Whatsapp for notification purposes, connect a Whatsapp Account.
                            </div>
                            <img id="qr-placeholder" src="https://img.freepik.com/premium-vector/qr-code-sample-vector-abstract-icon-isolated-white-background-vector-illustration_125869-2366.jpg" alt="Placeholder QR Code" class="block rounded-5 bg-light mb-2" style="aspect-ratio:1/1; max-width:200px">
                            <div class="wa-section wa-logs fs-italic">
                            </div>
                            <div class="wa-section wa-qrcode" style="display: none;">
                                <img id="qrcode" src="">
                                <div class="mt-1">Please scan QR Code above to link your Whatsapp</div>
                            </div>
                            <div class="wa-section wa-info mt-3 p-4 rounded-3 mb-0 bg-primary-subtle" style="display: none;">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ti ti-circle-check text-success fs-5"></i> Whatsapp connected as <span class="wa-number fw-bold"></span>.
                                </div>
                                <div class="mt-3">
                                    <a href="javascript:;" class="btn btn-disconnect-wa btn-danger fw-bold">disconnect</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script src="https://cdn.socket.io/4.0.1/socket.io.min.js"></script>
<script src="{{ asset('/js/whatsapp.js') }}"></script>
<script>
    var HOST = "{{ env('APP_WHATSAPP_SERVER') }}";
    jQuery(document).ready(function() {
        SystemSetting.init();
    });
</script>
@endsection
