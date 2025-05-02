@extends('layouts.mobile')

@section('css')

@endsection

@section('content')
<div class="p-4 px-3 bg-tanur-green pb-4">
    <div class="d-block text-decoration-none text-dark mb-2 text-white rounded-3 position-relative">
        <div class="badge bg-tanur-coklat text-white fs-1 fw-semibold mb-2">{{ $workspace->code }}</div>
        <div class="d-flex align-items-center gap-2 position-absolute top-0 end-0 m-2 ">
            <div class="bg-{{$workspace->getStatus()['color']}} rounded-3 p-1 px-2 text-white fs-1 fw-semibold">
                {{ $workspace->getStatus()['name'] }}
            </div>
            <div class="dropdown dropstart">
                <a href="#" class="text-white" id="dropdownMenuButton"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ti ti-dots-vertical fs-5"></i>
                </a>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <li>
                        <a href="{{route('agent.workspace.edit', [$workspace->agent_id, $workspace->id])}}" class="dropdown-item fs-2 d-flex align-items-center gap-2"><i
                                class="fs-2 ti ti-edit"></i>Edit</a>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item d-flex align-items-center fs-2 gap-2"
                            data-bs-toggle="modal" data-bs-target="#deleteModal-{{$workspace->id}}"><i
                                class="fs-2 ti ti-trash"></i>Delete</button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 mb-2">
            <img src="{{$workspace->requester->image_url}}" alt="Image workspace->requester {{$workspace->requester->name}}" class="d-block rounded-circle bg-dark" style="aspect-ratio:1/1" width="40">
            <div>
              <h6 class="fw-bold text-white mb-1 fs-2" style="white-space: nowrap">{{ $workspace->requester->name }}</h6>
              <div class="fs-1" style="white-space: nowrap">{{ $workspace->requester->level }}</div>
            </div>
        </div>

        <div class="fs-3 fw-semibold">{{$workspace->name}}</div>
        <div class="fs-1">{{$workspace->description ?? 'tidak ada deskripsi'}}</div>
        <div class="fs-2 mt-1 text-capitalize"><i class="ti ti-package me-2"></i> {{$workspace->product_type}}</div>
        <div class="fs-2 mt-1"><i class="ti ti-map-pin me-2"></i> {{ $workspace->city.'. '.$workspace->postal_code}}</div>
        <div class="fs-2 mt-1"><i class="ti ti-user-circle me-2"></i> {{$workspace->pic_name}}</div>
        <div class="fs-2 mt-1"><i class="ti ti-phone me-2"></i> {{$workspace->pic_phone}}</div>
        <div class="fs-2 mt-1"><i class="ti ti-mail me-2"></i> {{$workspace->pic_email}}</div>
        <div class="d-flex align-items-start flex-column gap-2 mt-2">
            <button data-modal-id="list-jamaah" class="btn-add-modal btn btn-light btn-sm rounded-pill"><i class="ti ti-user-circle me-1"></i> {{$workspace->pilgrims->count()}} Jamaah <i class="ti ti-arrow-right ms-2"></i></button>
            @if($workspace->is_approved)
                <div class="fs-2"><i class="ti ti-timeline-event me-1"></i> <span class="text-white fw-semibold">2</span> / 5 Stage Selesai</div>
                <div class="fs-2"><i class="ti ti-subtask me-1"></i> <span class="text-white fw-semibold">2</span> / 20 Task Selesai</div>
            @endif
        </div>
        
        <x-modal id="list-jamaah" title="List Jamaah">
            @foreach ($workspace->pilgrims as $pilgrim)
                <div class="p-3 rounded-3 bg-white text-dark mb-2 border border-2 border-dashed position-relative">
                    <div class="fs-2 mb-1 d-flex align-items-center gap-2"><i class="ti ti-user-circle me-2"></i>{{$pilgrim->name}}</div>
                    <div class="fs-2 mb-1 d-flex align-items-center gap-2"><i class="ti ti-phone me-2"></i>{{$pilgrim->phone}}</div>
                    <div class="fs-2 mb-1 d-flex align-items-center gap-2 {{$pilgrim->email ? '' : 'd-none'}}"><i class="ti ti-mail me-2"></i>{{$pilgrim->email}}</div>
                </div>
            @endforeach
        </x-modal>

        @if($workspace->getStatus()['message'])
        <div class="d-flex text-{{$workspace->getStatus()['color']}} align-items-center mt-2 fw-semibold gap-2">
            <i class="ti ti-alert-circle"></i>
            <div class="fs-1 text-white">{{$workspace->getStatus()['message']}}</div>
        </div>
        @endif

        @if($workspace->status != '0' && $workspace->status != '5')
        <div class="mt-1">
            <div class="fs-1 text-dark fw-semibold">Score Terkini</div>
            <div class="fs-4 fw-bolder text-warning">312</div>
        </div>
        @endif
    </div>
</div>
<div class="bg-light p-3 pb-4 rounded-top-3" style="margin-top: -1em">
    <h6 class="fs-3 fw-semibold text-dark">Approver</h6>
    <div class="py-2" style="overflow-y: hidden; overflow-x:auto; width:100%">
        <div class="d-flex align-items-center gap-2">
            @foreach($workspace->approvers as $approver)
                <div class="p-3 shadow-sm bg-white rounded-3">
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{$approver->image_url}}" alt="Image approver {{$approver->name}}" class="d-block rounded-circle bg-dark" style="aspect-ratio:1/1" width="40">
                        <div>
                          <h6 class="fw-bold mb-1 fs-2" style="white-space: nowrap">{{ $approver->name }}</h6>
                          <div class="fs-1" style="white-space: nowrap">{{ $approver->level }}</div>
                        </div>
                    </div>
                    <div class="d-flex mt-2 align-items-center justify-content-between gap-2">
                        <div class="badge fs-1 rounded-3 fw-semibold text-{{$workspace->approver_status[$approver->id]['color']}} bg-{{$workspace->approver_status[$approver->id]['color']}}-subtle">{{ $workspace->approver_status[$approver->id]['name'] }}</div>
                        <button data-modal-id="reason-modal" class="btn-add-modal btn btn-light btn-sm fs-4 rounded-circle" title="Alasan"><i class="ti ti-text-caption"></i></button>
                        <a target="_blank" href="{{'https://api.whatsapp.com/send?phone=6289671052050&text=Mohon memberikan tanggapan terhadap Approval workspace saya '.$workspace->requester->person['phone'].') dengan kode '.$workspace->code }}" class="btn btn-light rounded-circle btn-sm fs-4"><i class="ti ti-brand-whatsapp"></i></a>
                    </div>

                    <x-modal id="reason-modal" title="Detail Keputusan">
                        <div class="mb-2">
                            <div class="d-block fs-1 text-muted form-label mb-1">Alasan Keputusan</div>
                            <div class="fs-2 fw-semibold text-dark">{{$workspace->approver_status[$approver->id]['reason']}}</div>
                        </div>
                        <div class="mb-2">
                            <label for="" class="d-block fs-1 text-muted form-label">File Pendukung</label>
                            @if($workspace->approver_status[$approver->id]['attachment'])
                                <a href="{{ asset('storage/'.$workspace->approver_status[$approver->id]['attachment']) }}" class="fs-2 border p-2 px-3 d-inline-block border-primary rounded-3" target="_blank"><i class="ti ti-file me-2"></i> Lihat File</a>
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
<div class="bg-white p-3 rounded-top-3" style="margin-top: -1em">
    <h6 class="fs-3 fw-semibold text-dark">Stage</h6>
    @if($workspace->is_approved)
    @else
    <div class="p-3 fs-2 rounded-3 bg-light border border-2 text-center">
        Workspace ini belum disetujui oleh semua approver, silahkan tunggu hingga semua approver menyetujui workspace ini.
    </div>
    @endif
</div>
@endsection

@section('script')

@endsection