@extends('layouts.base')
@section('css')
<link rel="stylesheet" href="/assets/libs/select2/dist/css/select2.min.css">
@endsection

@section('content')
    <div class="card bg-info-subtle shadow-none position-relative overflow-hidden mb-4">
        <div class="card-body px-4 py-3">
            <div class="row align-items-center">
                <div class="col-9">
                    <h4 class="fw-semibold mb-8">Access</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a class="text-muted text-decoration-none" href="{{ route('home') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">Semua Access</li>
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
        <h1>Access</h1>
        <div style="aspect-ratio:1/1; width:3em; height:3em"
            class="bg-primary text-white d-flex align-items-center justify-content-center rounded-5 me-auto">
            {{ count($accesses) }}</div>
        <button class="btn btn-primary btn-al-primary" data-bs-toggle="modal" data-bs-target="#addModal">Tambah Baru</button>
    </div>
    <div class="table-responsive mb-4">
        <table class="table border text-nowrap mb-0 align-middle">
            <thead class="text-dark fs-4">
                <tr>
                    <th>
                        <h6 class="fs-4 fw-semibold mb-0">Role</h6>
                    </th>
                    <th>
                        <h6 class="fs-4 fw-semibold mb-0">Access</h6>
                    </th>
                    <th>
                        <h6 class="fs-4 fw-semibold mb-0">Timestamp</h6>
                    </th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($accesses as $access)
                <tr>
                    <td>
                        <div class="badge border border-primary text-primary rounded-3 fw-semibold fs-2">
                            {{ $access->name}}
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-start flex-wrap gap-1">
                            @forelse ($access->permissions as $permission)
                            <div class="badge bg-primary-subtle text-primary mb-1 fw-semibold fs-2">
                                {{ $permission->name }}
                            </div>
                            @empty
                            <div>Permission unset</div>
                            @endforelse
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column align-items-start gap-2">
                            <div class="badge bg-success-subtle text-success rounded-3 fw-semibold fs-2">Updated
                                at
                                : {{ $access->updated_at }}</div>
                            <div class="badge bg-primary-subtle text-primary rounded-3 fw-semibold fs-2">Created
                                at
                                : {{ $access->created_at }}</div>
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
                                    <a href="{{route('access.index', $access->id)}}" class="dropdown-item d-flex align-items-center gap-3"><i
                                            class="fs-4 ti ti-eye"></i>View</a>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item d-flex align-items-center gap-3"
                                        data-bs-toggle="modal" data-bs-target="#editModal-{{$access->id}}"><i
                                            class="fs-4 ti ti-pencil"></i>Edit Access</button>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item d-flex align-items-center gap-3"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal-{{$access->id}}"><i
                                            class="fs-4 ti ti-trash"></i>Delete</button>
                                </li>
                            </ul>
                        </div>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal-{{$access->id}}" tabindex="-1"
                            aria-labelledby="vertical-center-modal" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header d-flex align-items-center">
                                        <h4 class="modal-title" id="myLargeModalLabel">
                                            Edit Access {{$access->nama}}
                                        </h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('access.update', $access->id) }}" method="POST">
                                            @csrf
                                            <label for="name" class="form-label">Role Name</label>
                                            <input type="text" name="name" class="form-control mb-2" value="{{ old('name', $access->name) }}"
                                                placeholder="Nama access" required>
                                            <label class="form-label fw-semibold">Give an Access to</label>
                                            <div class="input-group">
                                                <span class="input-group-text px-6" id="basic-addon1"><i
                                                        class="ti ti-atom-2 fs-6"></i></span>
                                                <div style="flex-grow:1">
                                                    <select name="permissions[]" id="permissions-{{$access->id}}" class="select2 form-select" multiple="multiple">
                                                        <option value="">-- Pilih Aplikasi Permission --</option>
                                                        @foreach ($permissions as $permission)
                                                            <option value="{{$permission->name}}" 
                                                                {{ in_array($permission->name, old('permissions', $access->permissions->pluck('name')->toArray())) ? 'selected' : '' }}>
                                                                {{$permission->name}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            @error('permissions')
                                                <span class="invalid-feedback" role="alert">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                            <div class="d-flex gap-1 align-items-center justify-content-end">
                                                <button type="button"
                                                    class="btn bg-danger-subtle text-danger  waves-effect text-start"
                                                    data-bs-dismiss="modal">
                                                    Close
                                                </button>
                                                <button type="submit"
                                                    class="btn btn-primary btn-al-primary">Perbarui</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Modal -->
                        <div id="deleteModal-{{$access->id}}" class="modal fade" tabindex="-1"
                            aria-labelledby="danger-header-modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                                <div class="modal-content p-3 modal-filled bg-danger">
                                    <div class="modal-header modal-colored-header text-white">
                                        <h4 class="modal-title text-white" id="danger-header-modalLabel">
                                            Yakin ingin menghapus Access ?
                                        </h4>
                                        <button type="button" class="btn-close btn-close-white"
                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body" style="width: fit-content; white-space:normal">
                                        <h5 class="mt-0 text-white">Access {{$access->name}} akan dihapus</h5>
                                        <p class="text-white">Segala data yang berkaitan dengan Access tersebut juga akan dihapus secara permanen.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                            Close
                                        </button>
                                        <form action="{{route('access.destroy', $access->id)}}" method="POST">
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

    <!-- add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1"
        aria-labelledby="vertical-center-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h4 class="modal-title" id="myLargeModalLabel">
                        Add Access
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('access.store') }}" method="POST">
                        @csrf
                        <label for="name" class="form-label">Role Name</label>
                        <input type="text" name="name" class="form-control mb-2" value="{{ old('name') }}"
                            placeholder="Nama access" required>
                        <label class="form-label fw-semibold">Give an Access to</label>
                        <div class="input-group">
                            <span class="input-group-text px-6" id="basic-addon1"><i
                                    class="ti ti-atom-2 fs-6"></i></span>
                            <div style="flex-grow:1">
                                <select name="permissions[]" id="permissions" class="select2 form-select" multiple="multiple">
                                    <option value="">-- Pilih Aplikasi Permission --</option>
                                    @foreach ($permissions as $permission)
                                        <option value="{{$permission->name}}" 
                                            {{ in_array($permission->name, old('permissions') ?? []) ? 'selected' : '' }}>
                                            {{$permission->name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @error('permissions')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                        <div class="d-flex gap-1 align-items-center justify-content-end">
                            <button type="button"
                                class="btn bg-danger-subtle text-danger  waves-effect text-start"
                                data-bs-dismiss="modal">
                                Close
                            </button>
                            <button type="submit"
                                class="btn btn-primary btn-al-primary">Perbarui</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="/assets/libs/select2/dist/js/select2.full.min.js"></script>
    <script src="/assets/libs/select2/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').each(function () {
                let modal = $(this).closest('.modal'); // Cari modal terdekat
                $(this).select2({
                    dropdownParent: modal // Pasang dropdown di dalam modal
                });
            });
        });
    </script>
@endsection
