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
            @forelse ($histories as $history)
            <div class="row">
                <div class="col-2 h-100">
                    <div class="d-flex align-items-center justify-content-center position-relative h-100">
                        <div style="aspect-ratio:1/1;width:2em;height:2em" class="bg-{{ $history->color }} text-white d-flex align-items-center justify-content-center rounded-circle position-absolute top-0"><i class="ti ti-{{$history->icon}} fs-3"></i></div>
                        <div class="border-start bg-dark" style="width: 2px; height:5.5em"></div>
                    </div>
                </div>
                <div class="col-10">
                    <div>
                        <div class="fs-2 mb-2 fw-semibold">{{ $history->message }}</div>
                        <div class="fs-1 d-flex align-items-center gap-2 justify-content-between">
                            <div class=""><i class="ti ti-clock me-2"></i>{{ $history->time_ago }}</div>
                            <div class=""><i class="ti ti-calendar me-2"></i>{{ \Carbon\Carbon::parse($history->created_at)->format('d M Y H:i:s') }}</div>
                        </div>
                    </div>
                </div>
            </div>   
            @empty
            <div class="d-flex flex-column align-items-center justify-content-center text-center" style="height: 60vh">
                <img src="https://img.freepik.com/free-vector/empty-concept-illustration_114360-7416.jpg" alt="Empty Illustration" class="d-block w-100" style="max-width: 10em">
                <div class="text-center mt-3">
                    <h2 class="fw-semibold fs-3">Belum ada History</h2>
                    <p class="fs-1">History akan muncul disini</p>
                </div>
            </div>
            @endforelse
        </div>
    </section>
</div>
@endsection

@section('script')

@endsection