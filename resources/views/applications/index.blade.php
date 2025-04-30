@extends('layouts.base')
@section('content')
    <div class="card bg-info-subtle shadow-none position-relative overflow-hidden mb-4">
        <div class="card-body px-4 py-3">
            <div class="row align-items-center">
                <div class="col-9">
                    <h4 class="fw-semibold mb-8">Applications</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a class="text-muted text-decoration-none" href="{{ route('home') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">Semua Application</li>
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
        <h1>Application</h1>
        <div style="aspect-ratio:1/1; width:3em; height:3em"
            class="bg-primary text-white d-flex align-items-center justify-content-center rounded-5 me-auto">
            {{ count($applications) }}</div>
        <a href="{{ route('application.add') }}" class="btn btn-primary btn-al-primary">Tambah Baru</a>
    </div>
    <div class="table-responsive mb-4">
        <table class="table border text-nowrap mb-0 align-middle">
            <thead class="text-dark fs-4">
                <tr>
                    <th>
                        <h6 class="fs-4 fw-semibold mb-0">Application</h6>
                    </th>
                    <th>
                        <h6 class="fs-4 fw-semibold mb-0">Code</h6>
                    </th>
                    <th>
                        <h6 class="fs-4 fw-semibold mb-0">Timestamp</h6>
                    </th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($applications as $application)
                    <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="{{$application->image ? '/storage/'.$application->image : 'https://placehold.co/40'}}" class="rounded-2 object-fit-cover" width="40"
                                height="40" />
                            <div class="ms-3">
                                <h6 class="fs-4 fw-semibold mb-0">{{$application->name}}</h6>
                                <a href="{{url($application->url)}}" class="d-flex align-items-center gap-2"><i class="ti ti-link"></i> {{$application->url}}</a>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-start flex-column">
                            <div
                                class="badge bg-primary-subtle text-primary mb-1 fw-semibold fs-2 gap-1 d-inline-flex align-items-center">
                                <i class="ti ti-code fs-4"></i>
                                <span id="codeApp-{{$application->code}}">{{$application->code}}</span>
                                <button onclick="copyToClipboard('#codeApp-{{$application->code}}')" class="ms-2 btn btn-sm d-flex align-items-center btn-secondary justify-content-center">
                                    <i class="ti ti-copy"></i>
                                </button>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column align-items-start gap-2">
                            <div class="badge bg-success-subtle text-success rounded-3 fw-semibold fs-2">Updated
                                at
                                : {{ $application->updated_at }}</div>
                            <div class="badge bg-primary-subtle text-primary rounded-3 fw-semibold fs-2">Created
                                at
                                : {{ $application->created_at }}</div>
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
                                    <a href="{{route('application.edit', $application->id)}}" class="dropdown-item d-flex align-items-center gap-3"><i
                                            class="fs-4 ti ti-edit"></i>Edit</a>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item d-flex align-items-center gap-3"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal-{{$application->id}}"><i
                                            class="fs-4 ti ti-trash"></i>Delete</button>
                                </li>
                            </ul>
                        </div>

                        <!-- Delete Modal -->
                        <div id="deleteModal-{{$application->id}}" class="modal fade" tabindex="-1"
                            aria-labelledby="danger-header-modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                                <div class="modal-content p-3 modal-filled bg-danger">
                                    <div class="modal-header modal-colored-header text-white">
                                        <h4 class="modal-title text-white" id="danger-header-modalLabel">
                                            Yakin ingin menghapus Application ?
                                        </h4>
                                        <button type="button" class="btn-close btn-close-white"
                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body" style="width: fit-content; white-space:normal">
                                        <h5 class="mt-0 text-white">Application {{$application->name}} akan dihapus</h5>
                                        <p class="text-white">Segala data yang berkaitan dengan Application tersebut juga akan dihapus secara permanen.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                            Close
                                        </button>
                                        <form action="{{route('application.destroy', $application->id)}}" method="POST">
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
    </div>
@endsection

@section('scripts')
@endsection
