@extends('layouts.mobile')

@section('css')

@endsection

@section('content')
<div class="p-4 px-3 bg-tanur-green pb-4">
    <div class="d-block text-decoration-none text-dark mb-2 text-white rounded-3 position-relative">
        <div class="badge bg-tanur-coklat text-white fs-1 fw-semibold mb-2">{{ $approval->workspace->code }}</div>
        <div class="d-flex align-items-center gap-2 position-absolute top-0 end-0 m-2 ">
            <div class="bg-{{$approval->workspace->getStatus()['color']}} rounded-3 p-1 px-2 text-white fs-1 fw-semibold">
                {{ $approval->workspace->getStatus()['name'] }}
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 mb-2">
            <img src="{{$approval->workspace->requester->image_url}}" alt="Image workspace->requester {{$approval->workspace->requester->name}}" class="d-block rounded-circle bg-dark" style="aspect-ratio:1/1" width="40">
            <div>
              <h6 class="fw-bold text-white mb-1 fs-2" style="white-space: nowrap">{{ $approval->workspace->requester->name }}</h6>
              <div class="fs-1" style="white-space: nowrap">{{ $approval->workspace->requester->level }}</div>
            </div>
        </div>

        <div class="fs-3 fw-semibold">{{$approval->workspace->name}}</div>
        <div class="fs-1">{{$approval->workspace->description ?? 'tidak ada deskripsi'}}</div>
        <div class="fs-2 mt-1 text-capitalize"><i class="ti ti-package me-2"></i> {{$approval->workspace->product_type}}</div>
        <div class="fs-2 mt-1"><i class="ti ti-map-pin me-2"></i> {{ $approval->workspace->city.'. '.$approval->workspace->postal_code}}</div>
        <div class="fs-2 mt-1"><i class="ti ti-user-circle me-2"></i> {{$approval->workspace->pic_name}}</div>
        <div class="fs-2 mt-1"><i class="ti ti-phone me-2"></i> {{$approval->workspace->pic_phone}}</div>
        <div class="fs-2 mt-1"><i class="ti ti-mail me-2"></i> {{$approval->workspace->pic_email}}</div>
        <div class="d-flex align-items-start flex-column gap-2 mt-2">
            <button data-modal-id="list-jamaah" class="btn-add-modal btn btn-light btn-sm rounded-pill"><i class="ti ti-user-circle me-1"></i> {{$approval->workspace->pilgrims->count()}} Jamaah <i class="ti ti-arrow-right ms-2"></i></button>
            @if($approval->workspace->is_approved)
                <div class="fs-2"><i class="ti ti-timeline-event me-1"></i> <span class="text-white fw-semibold">2</span> / 5 Stage Selesai</div>
                <div class="fs-2"><i class="ti ti-subtask me-1"></i> <span class="text-white fw-semibold">2</span> / 20 Task Selesai</div>
            @endif
        </div>
        
        <x-modal id="list-jamaah" title="List Jamaah">
            @foreach ($approval->workspace->pilgrims as $pilgrim)
                <div class="p-3 rounded-3 bg-white text-dark mb-2 border border-2 border-dashed position-relative">
                    <div class="fs-2 mb-1 d-flex align-items-center gap-2"><i class="ti ti-user-circle me-2"></i>{{$pilgrim->name}}</div>
                    <div class="fs-2 mb-1 d-flex align-items-center gap-2"><i class="ti ti-phone me-2"></i>{{$pilgrim->phone}}</div>
                    <div class="fs-2 mb-1 d-flex align-items-center gap-2 {{$pilgrim->email ? '' : 'd-none'}}"><i class="ti ti-mail me-2"></i>{{$pilgrim->email}}</div>
                </div>
            @endforeach
        </x-modal>

        @if($approval->workspace->getStatus()['message'])
        <div class="d-flex text-{{$approval->workspace->getStatus()['color']}} align-items-center mt-2 fw-semibold gap-2">
            <i class="ti ti-alert-circle"></i>
            <div class="fs-1 text-white">{{$approval->workspace->getStatus()['message']}}</div>
        </div>
        @endif

        @if($approval->workspace->status != '0' && $approval->workspace->status != '5')
        <div class="mt-1">
            <div class="fs-1 text-white fw-semibold">Score Terkumpul</div>
            <div class="fs-4 fw-bolder text-warning">{{ $approval->workspace->live_score }}</div>
        </div>
        @endif
    </div>
</div>
<div class="bg-light p-3 pb-4 rounded-top-3" style="margin-top: -1em">
    <h6 class="fs-3 fw-semibold text-dark">Approver</h6>
    <div class="py-2" style="overflow-y: hidden; overflow-x:auto; width:100%">
        <div class="d-flex align-items-center gap-2">
            @foreach($approval->workspace->approvers as $approver)
                <div class="p-3 shadow-sm bg-white rounded-3">
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{$approver->image_url}}" alt="Image approver {{$approver->name}}" class="d-block rounded-circle bg-dark" style="aspect-ratio:1/1" width="40">
                        <div>
                          <h6 class="fw-bold mb-1 fs-2" style="white-space: nowrap">{{ $approver->name }} {{ $approver->id == session('agent_id') ? '(Anda)' : '' }}</h6>
                          <div class="fs-1" style="white-space: nowrap">{{ $approver->level }}</div>
                        </div>
                    </div>
                    <div class="d-flex mt-2 align-items-center justify-content-between gap-2">
                        <div class="badge fs-1 rounded-3 fw-semibold text-{{$approval->workspace->approver_status[$approver->id]['color']}} bg-{{$approval->workspace->approver_status[$approver->id]['color']}}-subtle">{{ $approval->workspace->approver_status[$approver->id]['name'] }}</div>
                        <button data-modal-id="reason-modal-{{$approval->id}}" class="btn-add-modal btn btn-light btn-sm fs-4 rounded-circle" title="Alasan"><i class="ti ti-text-caption"></i></button>
                        <a target="_blank" href="{{'https://api.whatsapp.com/send?phone=6289671052050&text=Mohon memberikan tanggapan terhadap Approval workspace saya '.$approval->workspace->requester->person['phone'].') dengan kode '.$approval->workspace->code }}" class="btn btn-light rounded-circle btn-sm fs-4"><i class="ti ti-brand-whatsapp"></i></a>
                    </div>

                    <x-modal id="reason-modal-{{$approval->id}}" title="Detail Keputusan">
                        <div class="mb-2">
                            <div class="d-block fs-1 text-muted form-label mb-1">Alasan Keputusan</div>
                            <div class="fs-2 fw-semibold text-dark">{{$approval->workspace->approver_status[$approver->id]['reason']}}</div>
                        </div>
                        <div class="mb-2">
                            <label for="" class="d-block fs-1 text-muted form-label">File Pendukung</label>
                            @if($approval->workspace->approver_status[$approver->id]['attachment'])
                                <a href="{{ asset('storage/'.$approval->workspace->approver_status[$approver->id]['attachment']) }}" class="fs-2 border p-2 px-3 d-inline-block border-primary rounded-3" target="_blank"><i class="ti ti-file me-2"></i> Lihat File</a>
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
<div class="bg-white rounded-top-3 p-3 position-sticky bottom-0" style="margin-top: -1em">
    @if($approval->status == '0')
    <button data-modal-id="make-decision" class="btn-add-modal btn border-0 fw-semibold w-100 fs-2 shadow-lg bg-tanur-coklat rounded-pill">Beri Keputusan</button>

    <x-modal id="make-decision" title="Buat Keputusan">
        <form action="{{ route('agent.approval.decision', $approval->id) }}" method="POST" enctype="multipart/form-data">
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
        <form action="{{ route('agent.approval.decision.update', $approval->id) }}" method="POST" enctype="multipart/form-data">
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

@section('script')

@endsection