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
            <form method="GET" action="{{ route('agent.workspace.list') }}">
                <div class="d-flex align-items-center gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" class="fs-2 form-control form-control-md rounded-pill" placeholder="Cari Workspace" aria-label="Search">
                    <button type="submit" class="btn bg-tanur-coklat p-0 rounded-circle d-flex align-items-center border-0 p-2">
                        <i class="ti ti-search"></i>
                    </button>
                </div>
            </form>
            <div class="py-2" style="overflow-y: hidden; overflow-x:auto; width:100%">
               <div class="d-flex align-items-center gap-2">
                    @php
                        $statuses = ['' => 'Semua', '0' => 'Pending', '1' => 'Berjalan', '2' => 'Pengajuan Stage', '3' => 'Stage Ditolak', '4' => 'Selesai', '5' => 'Ditolak'];
                        $currentStatus = request('status', ''); // pastikan ini sesuai dengan filter yang dikirim ke controller
                    @endphp
                    @foreach ($statuses as $key => $label)
                        @php
                            // Hitung jumlah workspace sesuai status
                            $query = \App\Models\Workspace::where('agent_id', session('agent_id'));
                            if ($key !== '') {
                                $query->where('status', (string)$key);
                            }
                            $count = $query->count();

                            // Tentukan apakah tombol ini aktif
                            $isActive = ($key === '' && $currentStatus === null) || $currentStatus === (string) $key;
                        @endphp
                        <a href="{{ route('agent.workspace.list', array_merge(request()->except('page'), ['status' => $key])) }}"
                        class="border {{ $isActive ? 'border-primary text-primary' : 'text-dark' }} p-1 px-2 fs-2 bg-light rounded-2 text-lowercase"
                        style="white-space:nowrap !important;">
                            <div class="d-inline-block me-1">
                                {{ $count }}
                            </div> {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

        </div>
        <div class="list bg-white p-2">
            @forelse ($workspaces as $workspace)
                <a href="{{ route('agent.workspace.show', $workspace->id) }}" class="p-3 d-block text-decoration-none text-dark mb-2 bg-light text-dark rounded-3 position-relative">
                    <div class="position-absolute top-0 end-0 bg-{{$workspace->getStatus()['color']}} rounded-3 p-1 px-2 text-white m-2 fs-1 fw-semibold">
                        {{ $workspace->getStatus()['name'] }}
                    </div>

                    <div class="fs-3 fw-semibold">{{$workspace->name}}</div>
                        <div class="fs-1">{{$workspace->description ?? 'tidak ada deskripsi'}}</div>
                        <div class="d-flex align-items-center gap-3 mt-2">
                        <div class="fs-2"><i class="ti ti-user-circle me-1"></i> {{$workspace->pilgrims->count()}} Jamaah</div>
                        <div class="fs-2"><i class="ti ti-timeline-event me-1"></i> <span class="text-primary fw-semibold">{{ $workspace->stageAnalytic()->finished }}</span> / {{ $workspace->stageAnalytic()->total }} Stage</div>
                        <div class="fs-2"><i class="ti ti-subtask me-1"></i> <span class="text-primary fw-semibold">{{ $workspace->taskAnalytic()->finished }}</span> / {{ $workspace->taskAnalytic()->total }} Task</div>
                    </div>
                    @if($workspace->getStatus()['message'])
                    <div class="d-flex text-{{$workspace->getStatus()['color']}} align-items-center mt-2 fw-semibold gap-2">
                        <i class="ti ti-alert-circle"></i>
                        <div class="fs-1 text-dark">{{$workspace->getStatus()['message']}}</div>
                    </div>
                    @endif

                    @if($workspace->status != '0')
                    <div class="mt-1">
                        <div class="fs-1 text-dark fw-semibold">Score Terkumpul</div>
                        <div class="fs-4 fw-bolder text-{{$workspace->getStatus()['color']}}">{{$workspace->live_score}}</div>
                    </div>
                    @endif
                </a>
            @empty
                <div class="p-3 fs-2 my-2 border border-2 border-dashed rounded-3 bg-light d-flex flex-column gap-2 align-items-center">
                    <div class="text-dark">Tidak ada workspace yang ditemukan</div>
                    <a href="{{route('agent.workspace.add')}}" class="btn btn-dark"><i class="ti ti-plus me-2"></i> Buat Baru</a>
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection

@section('script')

@endsection