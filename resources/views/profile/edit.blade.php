@extends('layouts.base')
@section('content')
    <div class="card bg-info-subtle shadow-none position-relative overflow-hidden mb-4">
        <div class="card-body px-4 py-3">
            <div class="row align-items-center">
                <div class="col-9">
                    <h4 class="fw-semibold mb-8">Edit Profile</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a class="text-muted text-decoration-none" href="{{ route('profile.index') }}">Profile</a>
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
            <img src="{{isset($user->image) ? filePath($user->image) : 'https://placehold.co/400'}}" id="placeholder-image" alt="User Image Preview" class="rounded-3 shadow w-100"
                style="object-fit:cover;">
            <img src="" id="preview-image" alt="User Image Preview" class="d-none rounded-3 shadow w-100"
                style="object-fit:cover;">
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="px-4 py-3 border-bottom">
                    <h5 class="card-title fw-semibold mb-0">Edit Profile</h5>
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
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Image Profile</label>
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
                                        <label class="form-label fw-semibold">Tetapkan Sebagai</label>
                                        <div class="input-group">
                                            <span class="input-group-text px-6" id="basic-addon1"><i
                                                    class="ti ti-lock fs-6"></i></span>
                                            <select name="role" id="role" class="form-select">
                                            @foreach ($roles as $role)
                                                <option value="{{$role->name}}" class="text-lowercase" {{$role->name == old('role', $user->getRoleNames()[0]) ? 'selected' : ''}}>{{$role->name}}</option>
                                            @endforeach
                                            </select>
                                        </div>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                @endrole
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Edit Profile</button>
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