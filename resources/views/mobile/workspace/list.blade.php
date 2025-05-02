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
        <h1 class="fw-semibold fs-3 mb-0 text-white text-center text-uppercase">Workspace</h1>
    </div>
    <section class="bg-white rounded-top-3 bg-dark">
        <div class="position-sticky top-0 bg-white rounded-top-3 p-3 pb-0" style="z-index: 99">
            <div class="d-flex align-items-center gap-2">
                <input type="text" class="fs-2 form-control form-control-md rounded-pill" placeholder="Cari Workspace" aria-label="Search" >
                <button class="btn bg-tanur-coklat p-0 rounded-circle d-flex align-items-center border-0 p-2"><i class="ti ti-search"></i></button>
            </div>
            <div class="py-2" style="overflow-y: hidden; overflow-x:auto; width:100%">
                <div class="d-flex align-items-center gap-2">
                    <button class="border border-primary p-1 px-2 fs-2 bg-light rounded-2 text-lowercase" style="white-space:nowrap !important;"> <div class="d-inline-block me-1 text-primary">123</div> Semua</button>
                    <button class="border p-1 px-2 fs-2 bg-light rounded-2 text-lowercase" style="white-space:nowrap !important;"> <div class="d-inline-block me-1 text-primary">21</div> Pending</button>
                    <button class="border p-1 px-2 fs-2 bg-light rounded-2 text-lowercase" style="white-space:nowrap !important;"> <div class="d-inline-block me-1 text-primary">32</div> Pengajuan</button>
                    <button class="border p-1 px-2 fs-2 bg-light rounded-2 text-lowercase" style="white-space:nowrap !important;"> <div class="d-inline-block me-1 text-primary">22</div> Berjalan</button>
                    <button class="border p-1 px-2 fs-2 bg-light rounded-2 text-lowercase" style="white-space:nowrap !important;"> <div class="d-inline-block me-1 text-primary">26</div> Selesai</button>
                </div>
            </div>
        </div>
        <div class="list bg-white p-2">
            @forelse ($workspaces as $workspace)
                <a href="{{ route('agent.workspace.show', ['id' => request()->route('id'), 'workspace_id' => $workspace->id]) }}" class="p-3 d-block text-decoration-none text-dark mb-2 bg-light text-dark rounded-3 position-relative">
                    <div class="position-absolute top-0 end-0 bg-{{$workspace->getStatus()['color']}} rounded-3 p-1 px-2 text-white m-2 fs-1 fw-semibold">
                        {{ $workspace->getStatus()['name'] }}
                    </div>

                    <div class="fs-3 fw-semibold">{{$workspace->name}}</div>
                        <div class="fs-1">{{$workspace->description ?? 'tidak ada deskripsi'}}</div>
                        <div class="d-flex align-items-center gap-3 mt-2">
                        <div class="fs-2"><i class="ti ti-user-circle me-1"></i> {{$workspace->pilgrims->count()}} Jamaah</div>
                        <div class="fs-2"><i class="ti ti-timeline-event me-1"></i> <span class="text-primary fw-semibold">2</span> / 5 Stage</div>
                        <div class="fs-2"><i class="ti ti-subtask me-1"></i> <span class="text-primary fw-semibold">2</span> / 20 Task</div>
                    </div>
                    @if($workspace->getStatus()['message'])
                    <div class="d-flex text-{{$workspace->getStatus()['color']}} align-items-center mt-2 fw-semibold gap-2">
                        <i class="ti ti-alert-circle"></i>
                        <div class="fs-1 text-dark">{{$workspace->getStatus()['message']}}</div>
                    </div>
                    @endif

                    @if($workspace->status != '0')
                    <div class="mt-1">
                        <div class="fs-1 text-dark fw-semibold">Score Terkini</div>
                        <div class="fs-4 fw-bolder text-warning">312</div>
                    </div>
                    @endif
                </a>
            @empty
                <div class="p-3 fs-2 my-2 border border-2 border-dashed rounded-3 bg-light d-flex flex-column gap-2 align-items-center">
                    <div class="text-dark">Belum ada Workspace</div>
                    <a href="{{route('agent.workspace.add', request()->route('id'))}}" class="btn btn-dark"><i class="ti ti-plus me-2"></i> Buat</a>
                </div>
            @endforelse
            <a href="{{ route('agent.workspace.show', ['id' => '25040904300304', 'workspace_id' => '81236123']) }}" class="p-3 d-block text-decoration-none text-dark mb-2 bg-light text-dark rounded-3 position-relative">
                <div class="position-absolute top-0 end-0 bg-secondary rounded-3 p-1 px-2 text-white m-2 fs-1 fw-semibold">
                Pending
                </div>

                <div class="fs-3 fw-semibold">Umroh Pak Sobron</div>
                <div class="fs-1">Calon Jamaah dari bapak combron</div>
                <div class="d-flex align-items-center gap-3 mt-2">
                <div class="fs-2"><i class="ti ti-user-circle me-1"></i> 2 Jamaah</div>
                </div>

            </a>
            <a href="{{ route('agent.workspace.show', ['id' => '25040904300304', 'workspace_id' => '81236123']) }}" class="p-3 d-block text-decoration-none text-dark mb-2 bg-light text-dark rounded-3 position-relative">
                <div class="position-absolute top-0 end-0 bg-warning rounded-3 p-1 px-2 text-white m-2 fs-1 fw-semibold">
                Berjalan
                </div>

                <div class="fs-3 fw-semibold">Umroh Pak Sobron</div>
                <div class="fs-1">Calon Jamaah dari bapak combron</div>
                <div class="d-flex align-items-center gap-3 mt-2">
                <div class="fs-2"><i class="ti ti-user-circle me-1"></i> 2 Jamaah</div>
                <div class="fs-2"><i class="ti ti-timeline-event me-1"></i> <span class="text-primary fw-semibold">2</span> / 5 Stage</div>
                <div class="fs-2"><i class="ti ti-subtask me-1"></i> <span class="text-primary fw-semibold">2</span> / 20 Task</div>
                </div>
                
                <div class="d-flex text-danger align-items-center mt-2 fw-semibold gap-2">
                <i class="ti ti-alert-triangle"></i>
                <div class="fs-1">Deadline stage 1 tersisa 3 hari lagi, stage 2 tersisa 6 hari lagi, stage 5 tersisa 12 hari lagi</div>
                </div>

                <div class="mt-1">
                <div class="fs-1 text-dark fw-semibold">Score Terkini</div>
                <div class="fs-4 fw-bolder text-warning">3122</div>
                </div>
            </a>
            <a href="{{ route('agent.workspace.show', ['id' => '25040904300304', 'workspace_id' => '81236123']) }}" class="p-3 d-block text-decoration-none text-dark mb-2 bg-light text-dark rounded-3 position-relative">
                <div class="position-absolute top-0 end-0 bg-dark rounded-3 p-1 px-2 text-white m-2 fs-1 fw-semibold">
                Pengajuan
                </div>

                <div class="fs-3 fw-semibold">Umroh Pak Sobron</div>
                <div class="fs-1">Calon Jamaah dari bapak combron</div>
                <div class="d-flex align-items-center gap-3 mt-2">
                <div class="fs-2"><i class="ti ti-user-circle me-1"></i> 2 Jamaah</div>
                <div class="fs-2"><i class="ti ti-timeline-event me-1"></i> <span class="text-primary fw-semibold">3</span>+<span class="text-warning fw-semibold">1</span> / 5 Stage</div>
                <div class="fs-2"><i class="ti ti-subtask me-1"></i> <span class="text-primary fw-semibold">10</span> / 20 Task</div>
                </div>

                <div class="mt-1">
                <div class="fs-1 text-dark fw-semibold">Score Terkini</div>
                <div class="fs-4 fw-bolder text-warning">212</div>
                </div>

            </a>

            <a href="{{ route('agent.workspace.show', ['id' => '25040904300304', 'workspace_id' => '81236123']) }}" class="p-3 d-block text-decoration-none text-dark mb-2 bg-light text-dark rounded-3 position-relative">
                <div class="position-absolute top-0 end-0 bg-danger rounded-3 p-1 px-2 text-white m-2 fs-1 fw-semibold">
                Ditolak
                </div>

                <div class="fs-3 fw-semibold">Umroh Pak Sobron</div>
                <div class="fs-1">Calon Jamaah dari bapak combron</div>
                <div class="d-flex align-items-center gap-3 mt-2">
                <div class="fs-2"><i class="ti ti-user-circle me-1"></i> 2 Jamaah</div>
                <div class="fs-2"><i class="ti ti-timeline-event me-1"></i> <span class="text-primary fw-semibold">2</span> / 5 Stage</div>
                <div class="fs-2"><i class="ti ti-subtask me-1"></i> <span class="text-primary fw-semibold">2</span> / 20 Task</div>
                </div>
                
                <div class="d-flex text-warning align-items-center mt-2 fw-semibold gap-2">
                <i class="ti ti-alert-circle"></i>
                <div class="fs-1 text-dark">Pengajuan Stage ditolak, klik untuk lihat detail</div>
                </div>

                <div class="mt-1">
                <div class="fs-1 text-dark fw-semibold">Score Terkini</div>
                <div class="fs-4 fw-bolder text-warning">312</div>
                </div>
            </a>

            <a href="{{ route('agent.workspace.show', ['id' => '25040904300304', 'workspace_id' => '81236123']) }}" class="p-3 d-block text-decoration-none text-dark mb-2 bg-light text-dark rounded-3 position-relative">
                <div class="position-absolute top-0 end-0 bg-success rounded-3 p-1 px-2 text-white m-2 fs-1 fw-semibold">
                Selesai
                </div>

                <div class="fs-3 fw-semibold">Umroh Pak Sobron</div>
                <div class="fs-1">Calon Jamaah dari bapak combron</div>
                <div class="d-flex align-items-center gap-3 mt-2">
                <div class="fs-2"><i class="ti ti-user-circle me-1"></i> 2 Jamaah</div>
                <div class="fs-2"><i class="ti ti-timeline-event me-1"></i> <span class="text-primary fw-semibold">5</span> / 5 Stage</div>
                <div class="fs-2"><i class="ti ti-subtask me-1"></i> <span class="text-primary fw-semibold">20</span> / 20 Task</div>
                </div>
                
                <div class="">
                <div class="fs-1 text-dark fw-semibold">Final Score</div>
                <div class="fs-4 fw-bolder text-success">3122</div>
                </div>
            </a>
        </div>
    </section>
</div>
@endsection

@section('script')

@endsection