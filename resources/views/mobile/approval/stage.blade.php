@extends('layouts.mobile')

@section('css')

@endsection

@section('content')
<div class="p-4 px-3 bg-tanur-coklat pb-4">
    <div class="d-block text-decoration-none text-dark mb-2 text-white rounded-3 position-relative">
        <div class="d-flex align-items-center gap-2 position-absolute top-0 end-0 m-2 ">
            <div class="fs-1 text-{{ $approval->workspaceStage->deadlineCount()['message']['color'] }} fw-semibold"> <i class="ti ti-clock me-2"></i> {{ $approval->workspaceStage->deadlineCount()['message']['text'] }}</div>
            <div class="bg-{{$approval->workspaceStage->getStatus()['color']}} rounded-3 p-1 px-2 text-white fs-1 fw-semibold">
                {{ $approval->workspaceStage->getStatus()['name'] }}
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 mb-2">
            <img src="{{$approval->requester->image_url}}" alt="Image workspace->requester {{$approval->requester->name}}" class="d-block rounded-circle bg-dark" style="aspect-ratio:1/1" width="40">
            <div>
              <h6 class="fw-bold text-white mb-1 fs-2" style="white-space: nowrap">{{ $approval->requester->name }}</h6>
              <div class="fs-1" style="white-space: nowrap">{{ $approval->requester->level }}</div>
            </div>
        </div>

        <div class="fs-3 fw-semibold">{{$approval->workspaceStage->stage->name}}</div>
        <div class="fs-1">{{$approval->workspaceStage->stage->description ?? 'tidak ada deskripsi'}}</div>
        @if($approval->workspaceStage->stage->attachments->count() > 0)
            <div class="d-flex align-items-center mt-2 gap-1">
            @foreach ($approval->workspaceStage->stage->attachments as $attachment)
                <a href="{{ asset('storage/'.$attachment->file) }}" class="fs-1 text-white p-1 px-2 d-inline-block bg-primary rounded-3" target="_blank"><i class="ti ti-file me-1"></i> {{$attachment->name}}</a>
            @endforeach
            </div>
        @endif

        <div class="mt-1">
            <div class="fs-1 text-white fw-semibold">Score Terkumpul</div>
            <div class="fs-4 fw-bolder text-dark">{{ $approval->workspaceStage->calculateScore()['final'] }}</div>
        </div>
    </div>
</div>
<div class="bg-light p-3 pb-4 rounded-top-3" style="margin-top: -1em">
    <h6 class="fs-3 fw-semibold text-dark">Stage Approver</h6>
    <div class="py-2" style="overflow-y: hidden; overflow-x:auto; width:100%">
        <div class="d-flex align-items-center gap-2">
            @foreach($approval->workspaceStage->approvers as $approver)
                <div class="p-3 shadow-sm bg-white rounded-3">
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{$approver->image_url}}" alt="Image approver {{$approver->name}}" class="d-block rounded-circle bg-dark" style="aspect-ratio:1/1" width="40">
                        <div>
                          <h6 class="fw-bold mb-1 fs-2" style="white-space: nowrap">{{ $approver->name }} {{ $approver->id == session('agent_id') ? '(Anda)' : '' }}</h6>
                          <div class="fs-1" style="white-space: nowrap">{{ $approver->level }}</div>
                        </div>
                    </div>
                    <div class="d-flex mt-2 align-items-center justify-content-between gap-2">
                        <div class="badge fs-1 rounded-3 fw-semibold text-{{$approval->workspaceStage->approver_status[$approver->id]['color']}} bg-{{$approval->workspaceStage->approver_status[$approver->id]['color']}}-subtle">{{ $approval->workspaceStage->approver_status[$approver->id]['name'] }}</div>
                        <button data-modal-id="reason-modal-{{$approver->id}}" class="btn-add-modal btn btn-light btn-sm fs-4 rounded-circle" title="Alasan"><i class="ti ti-text-caption"></i></button>
                        <a target="_blank" href="{{'https://api.whatsapp.com/send?phone=6289671052050&text=Mohon memberikan tanggapan terhadap Approval workspace saya '.$approval->requester->person['phone'].') dengan kode '.$approval->workspaceStage->code }}" class="btn btn-light rounded-circle btn-sm fs-4"><i class="ti ti-brand-whatsapp"></i></a>
                    </div>

                    <x-modal id="reason-modal-{{$approver->id}}" title="Detail Keputusan">
                        <div class="mb-2">
                            <div class="d-block fs-1 text-muted form-label mb-1">Alasan Keputusan</div>
                            <div class="fs-2 fw-semibold text-dark">{{$approval->workspaceStage->approver_status[$approver->id]['reason']}}</div>
                        </div>
                        <div class="mb-2">
                            <label for="" class="d-block fs-1 text-muted form-label">File Pendukung</label>
                            @if($approval->workspaceStage->approver_status[$approver->id]['attachment'])
                                <a href="{{ asset('storage/'.$approval->workspaceStage->approver_status[$approver->id]['attachment']) }}" class="fs-2 border p-2 px-3 d-inline-block border-primary rounded-3" target="_blank"><i class="ti ti-file me-2"></i> Lihat File</a>
                            @else
                                <div class="fs-2">Tidak ada file</div>
                            @endif
                        </div>
                    </x-modal>
                </div>
            @endforeach
        </div>
    </div>
</div>
<div class="bg-white p-3 pb-4 rounded-top-3" style="margin-top: -1em">
    <h6 class="fs-3 mb-3 fw-semibold text-dark">Pengerjaan Task</h6>
    <ul class="list-unstyled p-0">
        @forelse ($approval->workspaceStage->stage->tasks as $task)
        <li class="border-start rounded-3 mb-2 border-3 bg-light p-3 position-relative">
            <div class="position-absolute top-0 end-0 d-flex align-items-center gap-1" style=" margin-top: -0.5em">
                <div class="d-flex fs-2 align-items-center gap-0 bg-dark text-white rounded-pill p-2 justify-content-center fw-semibold" style="aspect-ratio:1/1 !important; width:2em; height:2em;">
                    {{ $task->score }}
                </div>
            </div>
            <div class="">
                <h5 class="fs-2 fw-semibold mb-1">{{$task->name}}</h5>
                <div class="fs-1">{{ $task->description ?? 'tidak ada deskripsi' }}</div>
                @if($task->attachments->count() > 0)
                    <div class="d-flex align-items-center mt-2 gap-1">
                    @foreach ($task->attachments as $attachment)
                        <a href="{{ asset('storage/'.$attachment->file) }}" class="fs-2 text-dark border p-1 px-2 d-inline-block border-primary rounded-3" target="_blank"><i class="ti ti-file me-1"></i> {{$attachment->name}}</a>
                    @endforeach
                    </div>
                @endif
            </div>

            <div class="border border-dark p-3 mt-2 rounded-3">
                <h6 class="fs-2 fw-semibold mb-2">Pengerjaan</h6>
                @php
                    $taskAnswer = $task->answer($approval->workspace_stage_id);
                @endphp
                <div class="fs-1">{{ $taskAnswer->answer_text ?? 'tidak ada jawaban dalam teks' }}</div>
                @if($taskAnswer->attachments->count() > 0)
                    <div class="d-flex align-items-center mt-2 gap-1">
                    @foreach ($taskAnswer->attachments as $attachment)
                        <a href="{{ asset('storage/'.$attachment->file) }}" class="fs-2 text-dark border p-1 px-2 d-inline-block border-primary rounded-3" target="_blank"><i class="ti ti-file me-1"></i> {{$attachment->name}}</a>
                    @endforeach
                    </div>
                @endif
            </div>
        </li>
        @empty
        <div class="p-3 fs-2 rounded-3 bg-light border border-2 text-center">
            Tidak ada task pada stage ini
        </div>
        @endforelse
    </ul>
</div>
<div class="rounded-top-3 p-3 {{ $approval->status == '0' ? 'bg-transparent' : 'bg-white' }} position-sticky" style="bottom:-4em">
    @if($approval->status == '0')
    <button data-modal-id="make-decision" class="btn-add-modal btn border-0 fw-semibold w-100 fs-2 shadow-lg bg-tanur-coklat rounded-pill">Beri Keputusan</button>

    <x-modal id="make-decision" title="Buat Keputusan">
        <form action="{{ route('agent.approval.stage.decision', $approval->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-2">
                <label for="decision" class="fs-1 text-muted form-label">Keputusan</label>
                <div class="d-flex gap-2">
                    <input type="radio" class="btn-check" name="decision" id="reject" value="reject" {{old('decision') == 'reject' ? 'checked' : ''}} required>
                    <label class="btn btn-outline-danger fs-2" for="reject">Tolak</label>

                    <input type="radio" class="btn-check" name="decision" id="approve" value="approve" {{old('decision') == 'approve' ? 'checked' : ''}} required>
                    <label class="btn btn-outline-success fs-2" for="approve">Setujui</label>
                </div>
            </div>
            <div class="mb-2">
                <label for="reason" class="fs-1 text-muted form-label">Alasan</label>
                <textarea class="form-control fs-2" rows="4" cols="10" placeholder="Alasan" id="reason" name="reason" required>{{ old('reason') }}</textarea>
            </div>
            <div class="mb-2">
                <label for="file" class="fs-1 text-muted form-label">File Pendukung</label>
                <input type="file" class="form-control fs-2 mb-1" id="file" name="attachment" accept=".pdf, .doc, .docx, .xls, .xlsx, .ppt, .pptx, .jpg, .jpeg, .png, .webp">
                <div class="fs-1">.pdf, .doc, .docx, .xls, .xlsx, .ppt, .pptx, .jpg, .jpeg, .png, .webp maksimal 5 MB</div>
            </div>
            <button type="submit" class="btn bg-tanur-green border-0 fw-semibold w-100">Putuskan</button>
        </form>
    </x-modal>
    @else
    <div class="">
        <div class="d-flex align-items-center justify-content-between gap-2">
            <h6 class="fs-3 fw-semibold">Keputusan</h6>
            <button data-modal-id="edit-decision" class="btn-add-modal btn btn-light fs-2"><i class="ti ti-edit me-2"></i> Edit</button>
        </div>
        <div class="mb-3">
            <label for="" class="d-block fs-1 text-muted form-label">Keputusan Anda</label>
            <div class="btn btn-{{ $approval->getStatus()['color'] }} fs-2">{{ $approval->getStatus()['name'] }}</div>
        </div>
        <div class="mb-3">
            <label for="" class="d-block fs-1 text-muted form-label">Alasan</label>
            <div class="fs-2">{{ $approval->reason }}</div>
        </div>
        <div class="mb-3">
            <label for="" class="d-block fs-1 text-muted form-label">File Pendukung</label>
            @if($approval->attachment)
                <a href="{{ asset('storage/'.$approval->attachment) }}" class="fs-2 border p-2 px-3 d-inline-block border-primary rounded-3" target="_blank"><i class="ti ti-file me-2"></i> Lihat File</a>
            @else
                <div class="fs-2">Tidak ada file</div>
            @endif
        </div>
    </div>
    <x-modal id="edit-decision" title="Buat Keputusan">
        <form action="{{ route('agent.approval.stage.decision.update', $approval->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-2">
                <label for="decision" class="fs-1 text-muted form-label">Keputusan</label>
                <div class="d-flex gap-2">
                    <input type="radio" class="btn-check" name="decision" id="reject" value="reject" {{old('decision') == 'reject' || $approval->status == '2' ? 'checked' : ''}} required>
                    <label class="btn btn-outline-danger fs-2" for="reject">Tolak</label>

                    <input type="radio" class="btn-check" name="decision" id="approve" value="approve" {{old('decision') == 'approve' || $approval->status == '1' ? 'checked' : ''}} required>
                    <label class="btn btn-outline-success fs-2" for="approve">Setujui</label>
                </div>
            </div>
            <div class="mb-2">
                <label for="reason" class="fs-1 text-muted form-label">Alasan</label>
                <textarea class="form-control fs-2" rows="4" cols="10" placeholder="Alasan" id="reason" name="reason" required>{{ old('reason', $approval->reason) }}</textarea>
            </div>
            <div class="mb-2">
                <label for="file" class="fs-1 text-muted form-label d-block">File Pendukung</label>
                <a href="{{ asset('storage/'.$approval->attachment) }}" class="fs-2 border p-2 px-3 {{ $approval->attachment ? 'd-inline-block' : 'd-none' }} border-primary rounded-3" target="_blank"><i class="ti ti-file me-2"></i> Lihat File Terkini</a>
                <input type="file" class="form-control fs-2 mb-1 {{ $approval->attachment ? 'mt-2' : '' }}" id="file" name="attachment" accept=".pdf, .doc, .docx, .xls, .xlsx, .ppt, .pptx, .jpg, .jpeg, .png, .webp">
                <div class="fs-1">.pdf, .doc, .docx, .xls, .xlsx, .ppt, .pptx, .jpg, .jpeg, .png, .webp maksimal 5 MB</div>
            </div>
            <button type="submit" class="btn bg-tanur-green border-0 fw-semibold w-100">Perbarui Keputusan</button>
        </form>
    </x-modal>
    @endif
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function(){
})
</script>
@endsection