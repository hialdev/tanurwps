@extends('layouts.base')
@section('content')
    <div class="card bg-info-subtle shadow-none position-relative overflow-hidden mb-4">
        <div class="card-body px-4 py-3">
            <div class="row align-items-center">
                <div class="col-9">
                    <h4 class="fw-semibold mb-8">Tasks</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a class="text-muted text-decoration-none" href="{{ route('home') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">Semua task</li>
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
    <div class="mb-3 d-flex align-items-center gap-2 justify-content-between">
        <h1>Task / Sub Stage</h1>
        <div style="aspect-ratio:1/1; width:3em; height:3em"
            class="bg-primary text-white d-flex align-items-center justify-content-center rounded-5 me-auto">
            {{ count($tasks) }}</div>
        <a href="{{ route('task.add') }}" class="btn btn-primary btn-al-primary"><i class="ti ti-plus me-2"></i>Tambah</a>
    </div>
    <div>
        <form action="{{ url()->current() }}" method="GET">
            <div class="row align-items-end mb-3 flex-wrap">
                <div class="col-md-4 mb-2">
                    <label for="search" class="form-label">Filter Kata</label>
                    <input type="text" class="form-control" placeholder="Cari Task" name="search"
                        value="{{ $filter->q ?? '' }}">
                </div>
                <div class="col-md-3 mb-2">
                    <label for="field" class="form-label">Urutkan Berdasarkan</label>
                    <select name="field" id="field" class="form-select">
                        @foreach (getModelAttributes('Task', []) as $atr)
                            <option value="{{ $atr }}" {{ $filter->field == $atr ? 'selected' : '' }}>
                                {{ toPascalCase($atr) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label for="order" class="form-label">Dengan urutan</label>
                    <select name="order" id="order" class="form-select">
                        <option value="newest" {{ $filter->order == 'desc' ? 'selected' : '' }}>Terbaru / Terbesar
                        </option>
                        <option value="oldest" {{ $filter->order == 'asc' ? 'selected' : '' }}>Terlama / Terkecil
                        </option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <div class="d-flex align-items-center gap-1">
                        <button type="submit" class="btn btn-primary w-100" style="white-space: nowrap">Apply</button>
                        <a href="{{ url()->current() }}" class="btn btn-secondary" style="white-space: nowrap">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1.2em" height="1.2em" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M22 12c0 5.523-4.477 10-10 10S2 17.523 2 12S6.477 2 12 2v2a8 8 0 1 0 4.5 1.385V8h-2V2h6v2H18a9.99 9.99 0 0 1 4 8" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="table-responsive mb-4">
        <table class="table border text-nowrap mb-0 align-middle">
            <thead class="text-dark fs-4">
                <tr>
                    <th>
                        <h6 class="fs-4 fw-semibold mb-0">Task</h6>
                    </th>
                    <th>
                        <h6 class="fs-4 fw-semibold mb-0">Detail</h6>
                    </th>
                    <th>
                        <h6 class="fs-4 fw-semibold mb-0">Stage</h6>
                    </th>
                    <th>
                        <h6 class="fs-4 fw-semibold mb-0">Attachments</h6>
                    </th>
                    <th>
                        <h6 class="fs-4 fw-semibold mb-0">Timestamp</h6>
                    </th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tasks as $task)
                <tr>
                    <td>
                        <div>
                            <div class="badge bg-primary-subtle text-primary fw-semibold fs-1 mb-1" style="">Urutan ke {{ $task->order ?? 'belum diatur' }}</div>
                            <div class="fw-normal fs-1 text-muted" style="white-space:normal;">Nama task
                            </div>
                            <h6 class="fw-semibold text-dark mb-1 fs-3" style="">{{ $task->name }}</h6>
                        </div>
                        <div>
                            <div class="fw-normal fs-1 text-muted" style="white-space:normal;">Score Point
                            </div>
                            <div class="text-primary fs-2 fw-semibold mb-1" style="white-space:normal;">{{ $task->score ?? 'belum diatur' }} Point</div>
                        </div>
                    </td>
                    <td>
                        <div>
                            <div class="fw-normal fs-1 text-muted" style="white-space:normal;">Description
                            </div>
                            <div class="text-dark fs-1 mb-1" style="white-space:normal;">{{ $task->description ?? 'tidak ada deskripsi' }}</div>
                        </div>
                    </td>

                    <td>
                        <a href="{{ route('stage.index', ['search' => $task->stage->name]) }}" class="d-block text-current-color">
                            <div>
                                <div class="badge bg-primary-subtle text-primary fw-semibold fs-1 mb-1" style="">Urutan ke {{ $task->stage->order ?? 'belum diatur' }}</div>
                                <h6 class="fw-semibold fs-3 text-dark mb-1" style="">{{ $task->stage->name }}</h6>
                            </div>
                            <div>
                                <div class="fw-normal fs-1 text-muted" style="white-space:normal;">Description
                                </div>
                                <div class="text-dark fs-1 mb-1" style="white-space:normal;">{{ $task->stage->description ?? 'tidak ada deskripsi' }}</div>
                            </div>
                        </a>
                    </td>
                    
                    <td>
                        <button type="button"
                            class="dropdown-item fs-2 text-center d-inline-flex p-2 px-3 align-items-center gap-2 bg-secondary text-white rounded-3"
                            data-bs-toggle="modal" data-bs-target="#attachmentModal-{{$task->id}}"><i
                                class="fs-4 ti ti-file"></i> {{ $task->attachments->count() }} File</button>

                        <!-- List Product modal -->
                        <div class="modal fade " id="attachmentModal-{{$task->id}}" tabindex="-1"
                            aria-labelledby="vertical-center-modal" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header d-flex align-items-center">
                                        <h4 class="modal-title" id="myLargeModalLabel">
                                            File Pendukung / Attachment
                                        </h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body pt-0">
                                        @forelse ($task->attachments as $attachment)
                                        <div class="d-flex align-items-center gap-3 justify-content-between p-3 rounded-3 bg-primary-subtle mb-2">
                                            <a href="{{ asset('storage/'.$attachment->file) }}" target="_blank" class="text-decoration-none text-dark d-flex align-items-center gap-2"><i class="fs-4 ti ti-file"></i> {{ $attachment->name }}</a>
                                            <button type="button" 
                                                class="btn btn-danger btn-sm delete-attachment-btn" 
                                                data-attachment-id="{{ $attachment->id }}" 
                                                data-attachment-name="{{ $attachment->name }}" 
                                                data-task-id="{{ $task->id }}"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteAttachmentModal">
                                                <i class="ti ti-trash-x"></i>
                                            </button>
                                        </div>
                                        @empty
                                        <div class="p-4 rounded-3 border-dashed border border-2 border-primary-subtle text-center text-muted text-dark">Tidak ada Attachment / File Pendukung</div>  
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                    </td>

                    <td>
                        <div class="d-flex flex-column align-items-start gap-2">
                            <div class="badge bg-success-subtle text-success rounded-3 fw-semibold fs-2">Updated
                                at
                                : {{ $task->updated_at }}</div>
                            <div class="badge bg-primary-subtle text-primary rounded-3 fw-semibold fs-2">Created
                                at
                                : {{ $task->created_at }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="dropdown dropstart">
                            <a href="#" class="text-muted" id="dropdownMenuButton"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ti ti-dots fs-5"></i>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <li>
                                    <a href="{{route('task.edit', $task->id)}}" class="dropdown-item d-flex align-items-center gap-3"><i
                                            class="fs-4 ti ti-edit"></i>Edit</a>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item d-flex align-items-center gap-3"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal-{{$task->id}}"><i
                                            class="fs-4 ti ti-trash"></i>Delete</button>
                                </li>
                            </ul>
                        </div>

                        <!-- Delete Modal -->
                        <div id="deleteModal-{{$task->id}}" class="modal fade" tabindex="-1"
                            aria-labelledby="danger-header-modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                                <div class="modal-content p-3 modal-filled bg-danger">
                                    <div class="modal-header modal-colored-header text-white">
                                        <h4 class="modal-title text-white" id="danger-header-modalLabel">
                                            Yakin ingin menghapus task ?
                                        </h4>
                                        <button type="button" class="btn-close btn-close-white"
                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body" style="width: fit-content; white-space:normal">
                                        <h5 class="mt-0 text-white">Task "{{$task->name}}" akan dihapus</h5>
                                        <p class="text-white">Segala data yang berkaitan dengan task tersebut juga akan dihapus secara permanen.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                            Close
                                        </button>
                                        <form action="{{route('task.destroy', $task->id)}}" method="POST">
                                          @csrf
                                          @method('delete')
                                          <button type="submit" class="btn btn-dark">Ya, Hapus</button>
                                        </form>
                                    </div>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                  <td colspan="5" class="text-center">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Delete Attachment Modal -->
        <div id="deleteAttachmentModal" class="modal fade" tabindex="-1"
            aria-labelledby="danger-header-modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                <div class="modal-content p-3 modal-filled bg-danger">
                    <div class="modal-header modal-colored-header text-white">
                        <h4 class="modal-title text-white" id="danger-header-modalLabel">
                            Yakin ingin Hapus Attachment ?
                        </h4>
                        <button type="button" class="btn-close btn-close-white"
                            data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="width: fit-content; white-space:normal">
                        <p class="text-white" id="deleteAttachmentMessage">Apakah Anda yakin ingin menghapus attachment ini?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            Close
                        </button>
                        <form id="deleteAttachmentForm" method="POST" class="d-inline">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-dark">Ya, Hapus</button>
                        </form>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    $('.delete-attachment-btn').on('click', function () {
        var taskId = $(this).data('task-id');
        var attachmentId = $(this).data('attachment-id');
        var attachmentName = $(this).data('attachment-name');

        // Update text di modal
        $('#deleteAttachmentMessage').text('Apakah Anda yakin ingin menghapus file attachment "' + attachmentName + '"?');

        // Update form action
        $('#deleteAttachmentForm').attr('action', '/tasks/' + taskId + '/attachments/' + attachmentId);
    });
});
</script>

@endsection
