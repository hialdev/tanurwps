@extends('layouts.base')
@section('content')
    <div class="card bg-info-subtle shadow-none position-relative overflow-hidden mb-4">
        <div class="card-body px-4 py-3">
            <div class="row align-items-center">
                <div class="col-9">
                    <h4 class="fw-semibold mb-8">Edit User</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a class="text-muted text-decoration-none" href="{{ route('user.index') }}">Users</a>
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

    <div class="row">
        <div class="col-md-4 h-100 mb-3">
            <img src="{{isset($user->image) ? '/storage/'.$user->image : 'https://placehold.co/400'}}" id="placeholder-image" alt="Flyer Image Preview" class="rounded-circle shadow w-100"
                style="aspect-ratio:1/1; object-fit:cover;">
            <img src="" id="preview-image" alt="Flyer Image Preview" class="d-none rounded-circle shadow w-100"
                style="aspect-ratio:1/1; object-fit:cover;">
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="px-4 py-3 border-bottom">
                    <h5 class="card-title fw-semibold mb-0">Edit User</h5>
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
                    <form action="{{ route('user.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Image User</label>
                            <div class="input-group">
                                <span class="input-group-text px-6" id="basic-addon1"><i
                                        class="ti ti-photo fs-6"></i></span>
                                <input type="file" name="image" class="form-control ps-2">
                            </div>
                            @error('image')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text px-6" id="basic-addon1"><i
                                                class="ti ti-user-circle fs-6"></i></span>
                                        <input type="text" name="name" class="form-control ps-2"
                                            placeholder="Name Surname" value="{{old('name', $user->name)}}">
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
                                    <label class="form-label fw-semibold">Jabatan</label>
                                    <div class="input-group">
                                        <span class="input-group-text px-6" id="basic-addon1"><i
                                                class="ti ti-user-exclamation fs-6"></i></span>
                                        <input type="text" name="position" class="form-control ps-2"
                                            placeholder="Jabatan" value="{{old('position', $user->position)}}">
                                    </div>
                                    @error('position')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text px-6" id="basic-addon1"><i
                                                class="ti ti-mail fs-6"></i></span>
                                        <input type="email" name="email" class="form-control ps-2"
                                            placeholder="user@mail.com" value="{{old('email', $user->email)}}">
                                    </div>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Phone / Whatsapp</label>
                                    <div class="input-group">
                                        <span class="input-group-text px-6" id="basic-addon1"><i
                                                class="ti ti-phone fs-6"></i></span>
                                        <input type="number" name="phone" class="form-control ps-2"
                                            placeholder="No. Phone / Whatsapp" value="{{old('phone', $user->phone)}}">
                                    </div>
                                    @error('phone')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="px-3 mb-4 pt-3 rounded-4 bg-light">
                            <div class="mb-2">Ubah Password (Kosongkan jika tidak ingin mengubahnya)</div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">New Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text px-6" id="basic-addon1"><i
                                                    class="ti ti-lock fs-6"></i></span>
                                            <input type="password" name="password" class="form-control ps-2">
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
                                        <label class="form-label fw-semibold">Confirm New Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text px-6" id="basic-addon1"><i
                                                    class="ti ti-lock fs-6"></i></span>
                                            <input type="password" name="confirm_password" class="form-control ps-2">
                                        </div>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                @role(['developer', 'admin'])
                                <div class="col-12">
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">Hak akses Sebagai (now: {{$user->getRoleNames()[0]}})</label>
                                        <div class="input-group">
                                            <span class="input-group-text px-6" id="basic-addon1"><i
                                                    class="ti ti-fingerprint fs-6"></i></span>
                                            <select name="role" id="role" class="form-select">
                                            @foreach ($roles as $role)
                                                <option value="{{$role->name}}" {{$role->name == old('role', $user->getRoleNames()[0]) ? 'selected' : ''}} class="text-lowercase">{{$role->name}}</option>
                                            @endforeach
                                            </select>
                                        </div>
                                        @error('role')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                @endrole
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Edit User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.querySelector('input[type="file"][name="image"]');
    const placeholder = document.getElementById('placeholder-image');
    const previewImage = document.getElementById('preview-image');

    fileInput.addEventListener('change', function (event) {
        const file = event.target.files[0];

        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();

            reader.onload = function (e) {
                previewImage.src = e.target.result; // Set preview image source
                previewImage.classList.remove('d-none'); // Show the preview image
                placeholder.classList.add('d-none'); // Hide the placeholder
            };

            reader.readAsDataURL(file);
        } else {
            // Reset if no file or file is not an image
            previewImage.src = '';
            previewImage.classList.add('d-none');
            placeholder.classList.remove('d-none');
        }
    });
});
</script>
@endsection