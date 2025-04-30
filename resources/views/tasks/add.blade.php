@extends('layouts.base')
@section('css')
<link rel="stylesheet" href="/assets/libs/select2/dist/css/select2.min.css">
@endsection
@section('content')
    <div class="card bg-info-subtle shadow-none position-relative overflow-hidden mb-4">
        <div class="card-body px-4 py-3">
            <div class="row align-items-center">
                <div class="col-9">
                    <h4 class="fw-semibold mb-8">Add New Task / Sub Stage</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a class="text-muted text-decoration-none" href="{{ route('task.index') }}">Tasks</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">Add New</li>
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

    <form action="{{ route('task.store') }}" method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-8 mb-2">
                <div class="card">
                    <div class="px-4 py-3 border-bottom">
                        <h5 class="card-title fw-semibold mb-0">Add Task</h5>
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
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Task ini Untuk Stage</label>
                                    <div class="input-group">
                                        <span class="input-group-text px-6" id="basic-addon1"><i
                                                class="ti ti-timeline-event fs-6"></i></span>
                                        <div style="flex-grow:1">
                                            <select name="stage_id" id="stage_id" class="select2 form-select">
                                                <option value="">-- Pilih Stage --</option>
                                                @foreach ($stages as $stage)
                                                    <option value="{{$stage->id}}" 
                                                        {{ old('stage_id') ? 'selected' : '' }}>
                                                        {{$stage->name}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text px-6" id="basic-addon1"><i
                                                class="ti ti-checklist fs-6"></i></span>
                                        <input type="text" name="name" class="form-control ps-2"
                                            placeholder="Nama Task" value="{{old('name')}}" required>
                                    </div>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Task Urutan Ke</label>
                                    <div class="input-group">
                                        <span class="input-group-text px-6" id="basic-addon1"><i
                                                class="ti ti-sort-descending-2 fs-6"></i></span>
                                        <input type="number" name="order" class="form-control ps-2"
                                            placeholder="Urutan Task" value="{{old('order')}}">
                                    </div>
                                    @error('order')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Score Point</label>
                            <div class="d-flex align-items-center gap-2">
                                <div class="input-group">
                                    <span class="input-group-text px-6" id="basic-addon1"><i
                                            class="ti ti-calendar-event fs-6"></i></span>
                                    <input type="number" name="score" class="form-control ps-2" min="1"
                                        placeholder="Score yang didapat ketika menyelesaikan Task ini" value="{{old('score')}}" required>
                                </div>
                                <div class="">Point</div>
                            </div>
                            <div class="fs-2 mt-2 text-muted">Score / Bobot / Point digunakan untuk menilai kinerja setiap Agent</div>
                            @error('score')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Description</label>
                            <div class="input-group">
                                <span class="input-group-text px-6" id="basic-addon1"><i
                                        class="ti ti-link fs-6"></i></span>
                                <textarea name="description" cols="30" rows="4" class="form-control ps-2">{{old('description')}}</textarea>
                            </div>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 h-100 mb-2">
                <h6 class="fs-4 fw-semibold"><i class="ti ti-files me-2"></i> File Pendukung</h6>
                <hr>
                <div id="attachments-box">
                    <div class="p-2 rounded-2 bg-primary-subtle">
                        <div class="d-flex align-items-center gap-1">
                            <div>
                                <input type="text" name="filenames[]" class="form-control form-control-sm mb-2 bg-white" placeholder="Nama File">
                                <input type="file" name="attachments[]" id="" class="form-control form-control-sm bg-white">
                            </div>
                            <div class="">
                                <button type="button" class="btn p-0 text-danger remove-attachment"><i class="ti ti-x"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary mt-2 btn-sm w-100" id="add-attachment"><i class="ti ti-plus"></i> Tambah File</button>
            </div>
            <div class="position-sticky bottom-0 col-12 mb-5">
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary w-100">Add Task</button>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('scripts')
<script src="/assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="/assets/libs/select2/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').each(function () {
            $(this).select2({
            });
        });
    });
</script>

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
    
});
</script>
@endsection