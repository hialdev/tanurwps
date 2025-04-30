@extends('layouts.base')

@section('content')
    <div class="card bg-info-subtle shadow-none position-relative overflow-hidden mb-4">
        <div class="card-body px-4 py-3">
            <div class="row align-items-center">
                <div class="col-9">
                    <h4 class="fw-semibold mb-8">Edit Stage</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a class="text-muted text-decoration-none" href="{{ route('stage.index') }}">Stages</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-3">
                    <div class="text-center mb-n5">
                        <img src="/assets/images/breadcrumb/ChatBc.png" alt="" class="img-fluid mb-n4" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('stage.update', $stage->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-8 mb-2">
                <div class="card">
                    <div class="px-4 py-3 border-bottom">
                        <h5 class="card-title fw-semibold mb-0">Edit Stage</h5>
                    </div>
                    <div class="card-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text px-6"><i class="ti ti-apps fs-6"></i></span>
                                        <input type="text" name="name" class="form-control ps-2" value="{{ old('name', $stage->name) }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Stage Urutan Ke</label>
                                    <div class="input-group">
                                        <span class="input-group-text px-6"><i class="ti ti-sort-descending-2 fs-6"></i></span>
                                        <input type="number" name="order" class="form-control ps-2" value="{{ old('order', $stage->order) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Masa Tenggat (Total Hari)</label>
                            <div class="d-flex align-items-center gap-2">
                                <div class="input-group">
                                    <span class="input-group-text px-6"><i class="ti ti-calendar-event fs-6"></i></span>
                                    <input type="number" name="deadline_days" class="form-control ps-2" min="1" value="{{ old('deadline_days', $stage->deadline_days) }}" required>
                                </div>
                                <div>Hari</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Description</label>
                            <div class="input-group">
                                <span class="input-group-text px-6"><i class="ti ti-link fs-6"></i></span>
                                <textarea name="description" cols="30" rows="4" class="form-control ps-2">{{ old('description', $stage->description) }}</textarea>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-4 h-100 mb-2">
                <h6 class="fs-4 fw-semibold"><i class="ti ti-files me-2"></i> File Pendukung</h6>
                <hr>

                {{-- Existing Attachments --}}
                @foreach($stage->attachments as $attachment)
                <div class="p-2 rounded-2 bg-primary-subtle mb-2 attachment-existing">
                    <div class="d-flex align-items-center gap-2 justify-content-between">
                        <a href="{{ asset('storage/' . $attachment->file) }}" target="_blank" class="text-decoration-none text-dark d-flex align-items-center gap-2">
                            <i class="fs-4 ti ti-file"></i> {{ $attachment->name }}
                        </a>
                        <button type="button" class="btn p-0 text-danger remove-existing-attachment" data-attachment-id="{{ $attachment->id }}">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                </div>
                @endforeach

                {{-- Hidden input untuk delete --}}
                <input type="hidden" name="deleted_attachments" id="deleted_attachments">

                {{-- New Attachments --}}
                <div id="attachments-box">
                    <div class="p-2 rounded-2 bg-primary-subtle">
                        <div class="d-flex align-items-center gap-1">
                            <div>
                                <input type="text" name="filenames[]" class="form-control form-control-sm mb-2 bg-white" placeholder="Nama File">
                                <input type="file" name="attachments[]" class="form-control form-control-sm bg-white">
                            </div>
                            <div>
                                <button type="button" class="btn p-0 text-danger remove-attachment"><i class="ti ti-x"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-secondary mt-2 btn-sm w-100" id="add-attachment">
                    <i class="ti ti-plus"></i> Tambah File
                </button>
            </div>

            <div class="position-sticky bottom-0 col-12 mb-5">
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary w-100">Update Stage</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    $('#add-attachment').on('click', function () {
        let attachmentHtml = `
            <div class="p-2 rounded-2 bg-primary-subtle attachment-item mt-2">
                <div class="d-flex align-items-center gap-1">
                    <div>
                        <input type="text" name="filenames[]" class="form-control form-control-sm mb-2 bg-white" placeholder="Nama File">
                        <input type="file" name="attachments[]" class="form-control form-control-sm bg-white">
                    </div>
                    <div>
                        <button type="button" class="btn p-0 text-danger remove-attachment"><i class="ti ti-x"></i></button>
                    </div>
                </div>
            </div>`;
        $('#attachments-box').append(attachmentHtml);
    });

    $(document).on('click', '.remove-attachment', function () {
        $(this).closest('.attachment-item').remove();
    });

    let deletedAttachments = [];

    $(document).on('click', '.remove-existing-attachment', function () {
        let attachmentId = $(this).data('attachment-id');
        deletedAttachments.push(attachmentId);
        $('#deleted_attachments').val(deletedAttachments.join(','));
        $(this).closest('.attachment-existing').remove();
    });
});
</script>
@endsection
