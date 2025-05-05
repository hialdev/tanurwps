@extends('layouts.mobile')

@section('css')

@endsection

@section('content')
<div class="p-3 pb-4 bg-tanur-green text-white">
    <div class="row fs-3 align-items-center">
        <div class="col-2">
            <a href="{{route('agent.workspace.show', $workspace->id)}}" class="btn border-0 bg-tanur-coklat d-flex align-items-center p-2 fs-5 text-white rounded-circle" style="aspect-ratio:1/1;"><i class="ti ti-arrow-narrow-left"></i></a>
        </div>
        <div class="col-9">
            <div class="d-flex align-items-center gap-3">
                <i class="ti ti-briefcase"></i>
                <h1 class="fw-semibold fs-3 text-white mb-0 text-center text-uppercase">{{ $workspace->name }}</h1>
            </div>
        </div>
    </div>
</div>
<div class="bg-light pb-4 p-3 rounded-top-3" style="margin-top: -1em">
    <div class="">
        <h5 class="fs-3 fw-semibold mb-1">{{$task->name}}</h5>
        <div class="fs-1">{{ $task->description ?? 'tidak ada deskripsi' }}</div>
        @if($task->attachments->count() > 0)
            <div class="d-flex align-items-center mt-2 gap-1">
                @foreach ($task->attachments as $attachment)
                    <a href="{{ asset('storage/'.$attachment->file) }}" class="fs-2 text-dark border p-1 px-2 d-inline-block border-primary rounded-3" target="_blank"><i class="ti ti-file me-1"></i> {{$attachment->name}}</a>
                @endforeach
            </div>
        @endif
        <div class="d-flex mt-2 align-items-center gap-2">
            <div class="">
                <div class="fs-1 text-muted fw-semibold">Score</div>
                <div class="fs-3 fw-semibold text-primary">+{{ $task->score }}</div>
            </div>
        </div>
    </div>
</div>
<div class="bg-white p-3 rounded-top-3" style="margin-top: -1em">
    <h2 class="fw-semibold fs-2">Laporan Pengerjaan</h2>
    <form action="{{route('agent.workspace.task.store', [$workspace->id, $task->id])}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="">
            <div class="mb-2">
                <label for="answer_text" class="fs-1 text-muted form-label">Jawab dengan teks</label>
                <textarea class="form-control fs-2" rows="7" cols="10" placeholder="Tulis detail pengerjaan (Jika terlalu panjang Lampirkan Saja dan isi box ini dengan teks 'Pengisian di lampiran')" id="answer_text" name="answer_text">{{ old('answer_text') }}</textarea>
            </div>
            <div class="mb-2">
                <div id="attachments-box">
                    <div class="fs-1 text-muted form-label">Jawab dengan Lampiran</div>
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
                <button type="button" class="btn border-primary text-primary bg-white mt-2 btn-sm w-100" id="add-attachment"><i class="ti ti-plus"></i> Tambah File</button>
            </div>
        </div>

        <div class="position-sticky bottom-0 pt-2">
            <button type="submit" class="btn border-0 bg-tanur-green text-white w-100 fs-2">Selesaikan Task</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
  $(document).ready(function(){

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