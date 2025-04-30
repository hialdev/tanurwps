@extends('layouts.base')
@section('content')
    <div class="card bg-info-subtle shadow-none position-relative overflow-hidden mb-4">
        <div class="card-body px-4 py-3">
            <div class="row align-items-center">
                <div class="col-9">
                    <h4 class="fw-semibold mb-8">Users</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a class="text-muted text-decoration-none" href="{{ route('home') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">Semua User</li>
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
        <h1>User</h1>
        <div style="aspect-ratio:1/1; width:3em; height:3em"
            class="bg-primary text-white d-flex align-items-center justify-content-center rounded-5 me-auto">
            {{ count($users) }}</div>
        <a href="{{ route('user.add') }}" class="btn btn-primary btn-al-primary">Tambah Baru</a>
    </div>
    <div class="table-responsive mb-4">
        <table class="table border text-nowrap mb-0 align-middle">
            <thead class="text-dark fs-4">
                <tr>
                    <th>
                        <h6 class="fs-4 fw-semibold mb-0">User</h6>
                    </th>
                    <th>
                        <h6 class="fs-4 fw-semibold mb-0">Contact</h6>
                    </th>
                    <th>
                        <h6 class="fs-4 fw-semibold mb-0">Role</h6>
                    </th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="{{$user->image ? '/storage/'.$user->image : '/assets/images/profile/user-2.jpg'}}" class="rounded-circle object-fit-cover" width="40"
                                height="40" />
                            <div class="ms-3">
                                <h6 class="fs-4 fw-semibold mb-0">{{$user->name}}</h6>
                                <span class="fw-normal">{{$user->position}}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-start flex-column">
                            <div
                                class="badge bg-primary-subtle text-primary mb-1 fw-semibold fs-2 gap-1 d-inline-flex align-items-center">
                                <i class="ti ti-mail fs-4"></i>{{$user->email}}
                            </div>
                            <div
                                class="badge {{ $user->phone ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'}} fw-semibold fs-2 gap-1 d-inline-flex align-items-center">
                                <i class="ti ti-phone fs-4"></i>{{$user->phone ?? 'Phone / Whatsapp belum ditentukan'}}
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                        <span class="badge text-bg-primary rounded-3 fw-semibold fs-2">
                            {{
                                count($user->getRoleNames()) > 0 
                                    ? $user->getRoleNames()[0]
                                    : 'Belum ada Peran'
                            }}
                        </span>
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
                                    <a href="{{route('user.edit', $user->id)}}" class="dropdown-item d-flex align-items-center gap-3"><i
                                            class="fs-4 ti ti-edit"></i>Edit</a>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item d-flex align-items-center gap-3"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal-{{$user->id}}"><i
                                            class="fs-4 ti ti-trash"></i>Delete</button>
                                </li>
                            </ul>
                        </div>

                        <!-- Delete Modal -->
                        <div id="deleteModal-{{$user->id}}" class="modal fade" tabindex="-1"
                            aria-labelledby="danger-header-modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                                <div class="modal-content p-3 modal-filled bg-danger">
                                    <div class="modal-header modal-colored-header text-white">
                                        <h4 class="modal-title text-white" id="danger-header-modalLabel">
                                            Yakin ingin menghapus User ?
                                        </h4>
                                        <button type="button" class="btn-close btn-close-white"
                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body" style="width: fit-content; white-space:normal">
                                        <h5 class="mt-0 text-white">User {{$user->name}} akan dihapus</h5>
                                        <p class="text-white">Segala data yang berkaitan dengan user tersebut juga akan dihapus secara permanen.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                            Close
                                        </button>
                                        <form action="{{route('user.destroy', $user->id)}}" method="POST">
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
