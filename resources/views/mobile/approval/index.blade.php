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
<div class="text-white">
    <div class="p-3 bg-tanur-green pb-4" style="margin-bottom: -0.7rem">
        <h1 class="fw-semibold fs-3 mb-0 text-white text-center text-uppercase">APPROVAL</h1>
    </div>
    <section class="bg-white rounded-top-3 bg-dark">
        <div class="position-sticky top-0 bg-white rounded-top-3 p-3 pb-0" style="z-index: 99">
            <div class="d-flex align-items-center gap-2">
                <input type="text" class="fs-2 form-control form-control-md rounded-pill" placeholder="Cari Approval" aria-label="Search" >
                <button class="btn bg-tanur-coklat p-0 rounded-circle d-flex align-items-center border-0 p-2"><i class="ti ti-search"></i></button>
            </div>
            <div class="py-2" style="overflow-y: hidden; overflow-x:auto; width:100%">
                <div class="d-flex align-items-center gap-2">
                    <button class="border border-primary p-1 px-2 fs-2 bg-light rounded-2 text-lowercase" style="white-space:nowrap !important;"> <div class="d-inline-block me-1 text-primary">123</div> Semua</button>
                    <button class="border p-1 px-2 fs-2 bg-light rounded-2 text-lowercase" style="white-space:nowrap !important;"> <div class="d-inline-block me-1 text-primary">21</div> Menunggu</button>
                    <button class="border p-1 px-2 fs-2 bg-light rounded-2 text-lowercase" style="white-space:nowrap !important;"> <div class="d-inline-block me-1 text-primary">32</div> Disetujui</button>
                    <button class="border p-1 px-2 fs-2 bg-light rounded-2 text-lowercase" style="white-space:nowrap !important;"> <div class="d-inline-block me-1 text-primary">22</div> Ditolak</button>
                </div>
            </div>
        </div>
        <div class="list bg-white p-2">
            @forelse($approvals as $approval)
            <a href="{{ route('agent.approval.show', $approval->id) }}" class="p-3 d-block text-decoration-none text-dark rounded-3 border mb-2 border-2 border-dashed">
              <div class="d-flex align-items-center gap-2 mb-2">
                <img src="https://placehold.co/100" alt="Image Agent Pengirim Approval" class="d-block rounded-circle" style="width:35px;aspect-ratio:1/1">
                <div>
                  <div class="fs-2 fw-semibold">{{$approval->requester->name}}</div>
                  <div class="fs-1 text-dark">{{$approval->requester->level}}</div>
                </div>
                <div class="fs-2 ms-auto fw-semibold"><i class="ti ti-clock me-2"></i>2 hari lalu</div>
              </div>
              <div class="d-flex align-items-center justify-content-between">
                <div class="fs-1 fw-semibold mb-1">Meminta Persetujuan</div>
                <div class="fs-1 fw-semibold text-{{ $approval->getStatus()['color'] }} mb-1">{{ $approval->getStatus()['name'] }}</div>
              </div>
              @if($approval->workspace)
              <button class="btn bg-tanur-green border-0 d-flex w-100 align-items-center gap-2"><i class="ti ti-briefcase"></i> Workspace <i class="ti ti-arrow-narrow-right ms-auto"></i></button>
              @elseif ($approval->stage)
              <button class="btn bg-tanur-coklat border-0 d-flex w-100 align-items-center gap-2"><i class="ti ti-timeline-event"></i> Stage <i class="ti ti-arrow-narrow-right ms-auto"></i></button>
              <div class="d-flex align-items-center text-danger mt-2 gap-2">
                <i class="ti ti-alert-triangle"></i>
                <div class="fs-1 fw-semibold">Deadline Stage ini sisa 3 hari lagi, beri keputusan segera</div>
              </div>
              @endif
            </a>
            @empty
            <div class="text-center p-3">
                <h2 class="fs-2 fw-semibold">Tidak ada data</h2>
                <p class="fs-1">Belum ada approval yang diajukan</p>
            </div>
            @endforelse
        </div>
    </section>
</div>
@endsection

@section('scripts')

@endsection