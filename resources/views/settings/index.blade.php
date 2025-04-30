@extends('layouts.base')
@section('content')
    <div class="card bg-info-subtle shadow-none position-relative overflow-hidden mb-4">
        <div class="card-body px-4 py-3">
            <div class="row align-items-center">
                <div class="col-9">
                    <h4 class="fw-semibold mb-8">Settings</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a class="text-muted text-decoration-none" href="{{ route('home') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">All Settings</li>
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
        <h1>Settings</h1>
        <button type="button" class="btn btn-primary btn-al-primary" data-bs-toggle="modal"
            data-bs-target="#addNewSetting">Add New</button>

        <!-- Vertically Add New Setting modal -->
        <div class="modal fade" id="addNewSetting" tabindex="-1" aria-labelledby="vertical-center-modal"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center">
                        <h4 class="modal-title" id="myLargeModalLabel">
                            Add New Setting
                        </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="p-2 rounded-3 bg-primary-subtle px-3 mb-2">
                            penggunaan kode <code class="ms-2">setting('<span id="code-preview"></span>')</code>
                        </div>
                        <form action="{{ route('setting.store') }}" method="POST">
                            @csrf

                            <div class="p-2 rounded-3 bg-light px-3 mb-2">
                                <div class="form-check form-switch py-2">
                                    <input class="form-check-input" name="s" type="checkbox" id="use-exist-group" {{count($uniqueGroups) > 0 ? 'checked' : ''}} />
                                    <label class="form-check-label" for="use-exist-group">gunakan group setting yang sudah ada</label>
                                </div>
                                <div id="exsists-group" class="{{count($uniqueGroups) <= 0 ? 'd-none' : ''}}">
                                  @if (count($uniqueGroups) > 0)
                                    <label for="group" class="form-label">Pilih Group</label>
                                    <select name="group" id="group" class="form-select mb-2">
                                        <option value="">Pilih Group</option>
                                        @foreach ($uniqueGroups as $grp)
                                            <option value="{{$grp->group_key}}">{{$grp->group}}</option>
                                        @endforeach
                                    </select>
                                  @else
                                    Tidak ada data group, silahkan buat baru group
                                  @endif
                                </div>
                                <div id="new-group" class="{{count($uniqueGroups) > 0 ? 'd-none' : ''}}">
                                    <label for="group_name" class="form-label">Buat Baru Group</label>
                                    <input type="text" name="group_name" class="form-control mb-2"
                                        value="{{ old('group_name') }}" placeholder="Nama Group Baru">
                                    <label for="group_key" class="form-label">Group Key</label>
                                    <input type="text" name="group_key" class="form-control mb-2"
                                        value="{{ old('group_key') }}" placeholder="Key bersifat unik tanpa spasi">
                                </div>
                            </div>


                            <label for="name" class="form-label">Setting Name</label>
                            <input type="text" name="name" class="form-control mb-2" value="{{ old('name') }}"
                              placeholder="Nama Setting"  required>
                            <label for="key" class="form-label">Setting Key</label>
                            <input type="text" name="key" class="form-control mb-2" value="{{ old('key') }}"
                              placeholder="Kode Unik"  required>
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" cols="30" rows="2" class="form-control mb-2" placeholder="Tambahkan keterangan">{{ old('description') }}</textarea>
                            <label for="input_type" class="form-label">Pilih Input Type</label>
                            <select name="input_type" id="input_type" class="form-select mb-2">
                                @foreach (config('al.input_type') as $key => $name)
                                    <option value="{{$key}}">{{$name}}</option>
                                @endforeach
                            </select>
                            @role('developer')
                            <div class="form-check form-switch py-2">
                                <input class="form-check-input" name="is_urgent" type="checkbox" id="is_urgent" value="1" />
                                <label class="form-check-label" for="is_urgent">Tetapkan sebagai urgent</label>
                            </div>
                            @endrole

                            <div class="d-flex gap-1 align-items-center justify-content-end">
                                <button type="button" class="btn bg-danger-subtle text-danger  waves-effect text-start"
                                    data-bs-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-primary btn-al-primary">Add Setting</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Nav tabs -->
    <ul class="nav nav-pills flex-column flex-sm-row " role="tablist">
        @foreach ($uniqueGroups as $group)
        <li class="nav-item text-sm-center">
            <a class="nav-link {{$loop->index == 0 ? 'active' : ''}}" data-bs-toggle="tab" href="#{{$group->group_key}}" role="tab">
                <span>{{$group->group}}</span>
            </a>
        </li>
        @endforeach
    </ul>
    <!-- Tab panes -->
    <div class="tab-content mt-2">
        @forelse ($groups as $groupKey => $gsettings)
            @php
                $sortedSettings = collect($gsettings)->sortBy('name');
            @endphp
            <div class="tab-pane {{$loop->index == 0 ? 'active' : ''}}" id="{{$groupKey}}" role="tabpanel">
                @foreach ($sortedSettings as $gs)
                    <div class="card mb-2">
                        <div class="card-body">
                            <form class="d-flex gap-3 align-items-start" action="{{route('setting.update', $gs->id)}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="w-100">
                                    <div class="d-flex mb-1 align-items-center gap-2">
                                        <h6 class="mb-0">{{$gs->name}}</h6><div class="p-1 px-2 bg-primary-subtle rounded-3"><code>setting('{{$gs->group_key.'.'.$gs->key}}')</code></div>
                                    </div>
                                    <p>{{$gs->description}}</p>
                                    <div class="mb-2">
                                        @include('partials.input', ['type' => $gs->input_type, 'name' => $gs->key, 'label' => $gs->name, 'data' => $gs->value])
                                    </div>
                                </div>
                                <div class="d-flex flex-column gap-1 align-items-center">
                                    <button type="button" title="Tetapkan Perubahan / Value"
                                        class="btn btn-sm d-flex align-items-center justify-content-center btn-primary"
                                        style="aspect-ratio:1/1"
                                        data-bs-toggle="modal" data-bs-target="#updateModal-{{$gs->id}}">
                                        <i class="fs-4 ti ti-wand"></i>
                                    </button>

                                    <!-- Update Modal -->
                                    <div id="updateModal-{{$gs->id}}" class="modal fade" tabindex="-1"
                                        aria-labelledby="primary-header-modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                                            <div class="modal-content p-3 modal-filled bg-primary">
                                                <div class="modal-header modal-colored-header text-white">
                                                    <h4 class="modal-title text-white" id="primary-header-modalLabel">
                                                        Yakin ingin memperbarui setting {{$gs->name}}?
                                                    </h4>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body" style="width: fit-content; white-space:normal">
                                                    <h5 class="mt-0 text-white">Nilai Setting "{{$gs->name}}" akan diperbarui</h5>
                                                    <p class="text-white">Pastikan nilai yang diperbarui telah benar / sesuai</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                                        Close
                                                    </button>
                                                    <input type="hidden" name="input_type" value="{{$gs->input_type}}">
                                                    <input type="hidden" name="key" value="{{$gs->key}}">
                                                    <button type="submit" class="btn btn-dark">Ya, Update</button>
                                                </div>
                                            </div>
                                            <!-- /.modal-content -->
                                        </div>
                                        <!-- /.modal-dialog -->
                                    </div>
                                        
                                    <button type="button" title="Kosongkan Nilai"
                                        class="btn btn-sm d-flex align-items-center justify-content-center btn-secondary"
                                        style="aspect-ratio:1/1"
                                        data-bs-toggle="modal" data-bs-target="#clearModal-{{$gs->id}}">
                                        <i class="fs-4 ti ti-refresh"></i>
                                    </button>

                                    <button type="button" title="Hapus Setting"
                                        class="btn btn-sm d-flex align-items-center justify-content-center btn-danger"
                                        style="aspect-ratio:1/1"
                                        data-bs-toggle="modal" data-bs-target="#deleteSettingModal-{{$gs->id}}" {{$gs->is_urgent ? 'disabled' : ''}}>
                                        <i class="fs-4 ti ti-trash"></i>
                                    </button>

                                    @role('developer')
                                    <button type="button" title="Hapus Setting"
                                        class="btn btn-sm d-flex align-items-center justify-content-center btn-danger {{!$gs->is_urgent ? 'd-none' : ''}}"
                                        style="aspect-ratio:1/1"
                                        data-bs-toggle="modal" data-bs-target="#forceModal-{{$gs->id}}">
                                        <i class="fs-4 ti ti-trash-off"></i>
                                    </button>
                                    @endrole
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Clear Modal -->
                    <div id="clearModal-{{$gs->id}}" class="modal fade" tabindex="-1"
                        aria-labelledby="clear-header-modalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                            <div class="modal-content p-3 modal-filled bg-secondary">
                                <div class="modal-header modal-colored-header text-white">
                                    <h4 class="modal-title text-white" id="secondary-header-modalLabel">
                                        Yakin ingin mengosongkan nilai setting {{$gs->name}}?
                                    </h4>
                                    <button type="button" class="btn-close btn-close-white"
                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" style="width: fit-content; white-space:normal">
                                    <h5 class="mt-0 text-white">Nilai Setting "{{$gs->name}}" akan dikosongkan</h5>
                                    <p class="text-white">Apabila nilai berbentuk file / image maka semuanya akan dibersihkan juga</p>
                                </div>
                                <div class="modal-footer">
                                    <form action="{{route('setting.clear', $gs->id)}}" method="post">
                                        @csrf
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                          Close
                                        </button>
                                        <button type="submit" class="btn btn-dark">Ya, Bersihkan</button>
                                    </form>
                                </div>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>

                    <!-- Delete Modal -->
                    <div id="deleteSettingModal-{{$gs->id}}" class="modal fade" tabindex="-1"
                        aria-labelledby="danger-header-modalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                            <div class="modal-content p-3 modal-filled bg-danger">
                                <div class="modal-header modal-colored-header text-white">
                                    <h4 class="modal-title text-white" id="danger-header-modalLabel">
                                        Yakin ingin menghapus setting ?
                                    </h4>
                                    <button type="button" class="btn-close btn-close-white"
                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" style="width: fit-content; white-space:normal">
                                    <h5 class="mt-0 text-white">Setting "{{$gs->name}}" akan dihapus</h5>
                                    <p class="text-white">Segala data yang berkaitan dengan setting tersebut juga akan dihapus secara permanen.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                        Close
                                    </button>
                                    <form action="{{route('setting.destroy', $gs->id)}}" method="POST">
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

                    @role('developer')
                    <!-- Force Delete Modal -->
                    <div id="forceModal-{{$gs->id}}" class="modal fade {{!$gs->is_urgent ? 'd-none' : ''}}" tabindex="-1"
                        aria-labelledby="danger-header-modalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                            <div class="modal-content p-3 modal-filled bg-danger">
                                <div class="modal-header modal-colored-header text-white">
                                    <h4 class="modal-title text-white" id="danger-header-modalLabel">
                                        Yakin ingin menghapus permanen setting ?
                                    </h4>
                                    <button type="button" class="btn-close btn-close-white"
                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" style="width: fit-content; white-space:normal">
                                    <h5 class="mt-0 text-white">Setting "{{$gs->name}}" akan dihapus</h5>
                                    <p class="text-white">Segala data yang berkaitan dengan setting tersebut juga akan dihapus secara permanen.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                        Close
                                    </button>
                                    <form action="{{route('setting.force', $gs->id)}}" method="POST">
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
                    @endrole
                @endforeach
            </div>
        @empty
        <div class="card">
            <div class="card-body">
                <div class="text-center">Data Setting tidak ditemukan, silahkan tambah baru</div>
            </div>
        </div>
        @endforelse
    </div>
@endsection
@section('scripts')
<script>
    var groupCheckbox = $('#use-exist-group');
    var groupExist = $('#exsists-group');
    var groupNew = $('#new-group');
    var key = $('input[name="key"]');
    var group = $('select[name="group"]');
    var code = $('span#code-preview');

    groupCheckbox.on('change', function() {
        if ($(this).is(':checked')) {
            groupExist.removeClass('d-none');
            groupNew.addClass('d-none');
            $('input[name="group_name"]').val('');
            $('input[name="group_key"]').val('');
        } else {
            groupExist.addClass('d-none');
            groupNew.removeClass('d-none');
            group.val('');
        }
        setCode();
    });

    $('input[name="name"]').on('input', function () {
        const nameValue = $(this).val();
        key.val(makeSlug(nameValue)).trigger('change');
    });

    $('input[name="group_name"]').on('input', function () {
        const nameValue = $(this).val();
        $('input[name="group_key"]').val(makeSlug(nameValue)).trigger('change');
    });

    group.on('change', function () {
        setCode()
    });
    
    $('input[name="group_key"]').on('change', function () {
        setCode()
    });

    key.on('change', function () {
        setCode();
    });

    const setCode = () => {
        const keyVal = key.val() || ''; // Default ke string kosong jika key.val() tidak terdefinisi
        const groupVal = groupCheckbox.is(':checked') 
            ? group.val() // Jika checkbox aktif, gunakan nilai dari 'group'
            : $('input[name="group_key"]').val(); // Jika tidak, gunakan input 'group_key'

        console.log('Group Value:', group.val(), groupVal, 'Checkbox Checked:', groupCheckbox.checked);
        const text = groupVal ? `${groupVal}.${keyVal}` : '.'+keyVal;
        code.text(text);
    };
</script>
@endsection