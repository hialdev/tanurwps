@extends('layouts.auth')
@section('content')
<div id="main-wrapper">
    <div class="position-relative overflow-hidden min-vh-100 w-100 d-flex align-items-center justify-content-center">
      <div class="d-flex align-items-center justify-content-center w-100">
        <div class="row justify-content-center w-100">
          <div class="col-lg-4">
            <div class="text-center">
              <img src="/assets/images/backgrounds/403.svg" alt="" class="img-fluid mb-5" width="500">
              <h1 class="fw-semibold mb-7 fs-9">403 Tidak Ada Idzin!</h1>
              <h4 class="fw-semibold mb-7 text-muted">Maaf peran akun anda sebagai "{{auth()?->user()?->getRoleNames()[0] ?? 'Haha your location locked'}}" tidak dapat mengakses aksi ini, jika anda bermaksud bertindak illegal IP dan Lokasi anda telah dicatat oleh sistem, Segala bentuk tindakan ilegal akan diproses sesuai dengan hukum yang berlaku.</h4>
              <a class="btn btn-primary" href="{{url()->previous()}}" role="button">Kembali</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection