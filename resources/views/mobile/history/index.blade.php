@extends('layouts.mobile')

@section('css')
<style>
.baseapp{
    position: relative;
    background: linear-gradient(180deg, #133057 0%, #065F6A 23.43%, #67ACAC 54.38%, #C7E0E0 72.07%, rgba(255, 255, 255, 0.00) 91.97%);
}
.bg-gradient-tanur{
    background: linear-gradient(180deg, #133057 0%, #065F6A 23.43%, #67ACAC 54.38%, #C7E0E0 72.07%, rgba(255, 255, 255, 0.00) 91.97%);
}
/* Responsive: hilangkan frame saat layar kecil */
@media (max-width: 576px) {
    .baseapp {
        max-height: auto;
    }
}
</style>
@endsection

@section('content')
<div class="">
    <div class="p-3 bg-tanur-green pb-4" style="margin-bottom: -0.7rem">
        <h1 class="fw-semibold fs-3 mb-0 text-white text-center text-uppercase">History</h1>
    </div>
    <section class="bg-white rounded-top-3 bg-dark">
        <div class="bg-white rounded-top-3 p-3 pb-0" style="z-index: 99">
            <div class="d-flex flex-column align-items-center justify-content-center text-center" style="height: 60vh">
                <img src="https://img.freepik.com/free-vector/empty-concept-illustration_114360-7416.jpg" alt="Empty Illustration" class="d-block w-100" style="max-width: 10em">
                <div class="text-center mt-3">
                    <h2 class="fw-semibold fs-3">Belum ada History</h2>
                    <p class="fs-1">History akan muncul disini</p>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('script')

@endsection