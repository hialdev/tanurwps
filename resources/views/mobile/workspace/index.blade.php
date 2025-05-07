@extends('layouts.mobile')

@section('css')
<style>
.baseapp{
    position: relative;
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
<div class="baseapp text-white">
    <div class="p-3">
      <h1 class="fs-5 fw-bolder text-white my-3">Ahlan Wasahlan, Agent Syiar Tanur Muthmainnah</h1>
      <div class="d-flex align-items-center gap-3">
          <img src="{{$agent->image_url}}" alt="Image Agent {{$agent->name}}" class="d-block rounded-circle bg-dark" style="aspect-ratio:1/1" width="75">
          <div>
            <div class="bg-tanur-coklat badge fs-2 fw-bolder mb-1">{{$agent->agent_no}}</div>
            <h6 class="fw-bold text-white mb-1 fs-4">{{ $agent->name }}</h6>
            <div class="fs-2">{{ $agent->level }}</div>
          </div>
      </div>

      <div class="card bg-tanur-green text-white mt-3 shadow-none">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-5">
              <div class="fs-2 text-white">Total Score</div>
              <div class="fw-bolder fs-5">{{$count->total_score}}</div>
            </div>
            <div class="col-7">
              <div class="fs-2 text-white">Workspace</div>
              <div class="d-flex align-items-center gap-2 rounded-2">
                <div class="fs-3 text-white fw-bolder"><i class="ti ti-checklist me-1"></i>{{ $count->total_workspace }}</div>
                <div class="fs-3 text-warning fw-bolder"><i class="ti ti-clock me-1"></i>{{ $count->workspace->ongoing }}</div>
                <div class="fs-3 text-success fw-bolder"><i class="ti ti-heart-handshake me-1"></i>{{ $count->workspace->finish }}</div>
                <div class="fs-3 text-danger fw-bolder"><i class="ti ti-x me-1"></i>{{ $count->workspace->rejected }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <section class="bg-white rounded-top-4 p-3">
      <div class="d-flex mb-2 align-items-center justify-content-between">
        <h6 class="fw-bolder mb-0">Workspace Terbaru</h6>
        <a href="{{route('agent.workspace.index')}}" class="fs-2 tanur-link">Semua<i class="ti ti-arrow-narrow-right ms-2"></i></a>
      </div>
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
                  <div class="fs-1 text-dark fw-semibold">Score Terkini</div>
                  <div class="fs-4 fw-bolder text-warning">312</div>
              </div>
              @endif
          </a>
      @empty
          <div class="p-3 fs-2 my-2 border border-2 border-dashed rounded-3 bg-light d-flex flex-column gap-2 align-items-center">
              <div class="text-dark">Belum ada Workspace</div>
              <a href="{{route('agent.workspace.add')}}" class="btn btn-dark"><i class="ti ti-plus me-2"></i> Buat</a>
          </div>
      @endforelse
    </section>
</div>
<div class="bg-light p-1"></div>
<section class="p-3">
  <div class="d-flex mb-2 align-items-center justify-content-between">
    <h6 class="fw-bolder mb-0">Approval Terbaru</h6>
    <a href="{{ route('agent.approval.index') }}" class="fs-2 tanur-link">Semua<i class="ti ti-arrow-narrow-right ms-2"></i></a>
  </div>
  @forelse($approvals as $approval)
    <a href="{{ $approval->workspace ? route('agent.approval.show', $approval->id) : route('agent.approval.stage.show', $approval->id) }}" class="p-3 d-block text-decoration-none text-dark rounded-3 border mb-2 border-2 border-dashed">
      <div class="d-flex align-items-center gap-2 mb-2">
        <img src="https://placehold.co/100" alt="Image Agent Pengirim Approval" class="d-block rounded-circle" style="width:35px;aspect-ratio:1/1">
        <div>
          <div class="fs-2 fw-semibold">{{$approval->requester->name}}</div>
          <div class="fs-1 text-dark">{{$approval->requester->level}}</div>
        </div>
        <div class="fs-1 ms-auto fw-semibold"><i class="ti ti-clock me-2"></i>{{ $approval->time_ago }}</div>
      </div>
      <div class="d-flex align-items-center justify-content-between">
        <div class="fs-1 fw-semibold mb-1">Meminta Persetujuan</div>
        <div class="fs-2 fw-semibold text-{{ $approval->getStatus()['color'] }} mb-1">{{ $approval->getStatus()['name'] }}</div>
      </div>
      @if($approval->workspace)
      <button class="btn bg-tanur-green border-0 d-flex w-100 align-items-center gap-2"><i class="ti ti-briefcase"></i> {{$approval->workspace->name}} <i class="ti ti-arrow-narrow-right ms-auto"></i></button>
      @elseif($approval->workspaceStage)
      <button class="btn bg-tanur-coklat border-0 d-flex w-100 align-items-center gap-2"><i class="ti ti-timeline-event"></i> {{$approval->workspaceStage->stage->name}} <i class="ti ti-arrow-narrow-right ms-auto"></i></button>
      <div class="d-flex align-items-center text-{{ $approval->workspaceStage->deadlineCount()['message']['color'] }} mt-2 gap-2">
        <i class="ti ti-alert-circle"></i>
        <div class="fs-1 fw-semibold">Deadline Stage {{ $approval->workspaceStage->deadlineCount()['message']['text'] }}</div>
      </div>
      @endif
    </a>
  @empty
  <div class="p-3 fs-2 border border-2 border-dashed rounded-3 bg-light text-center">
      <p class="fs-2 mb-0">Belum ada permintaan Approval</p>
  </div>
  @endforelse
</section>
@endsection

@section('script')

@endsection