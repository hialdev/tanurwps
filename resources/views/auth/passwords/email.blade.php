@extends('layouts.auth')

@section('content')
<section class="bg-light">
    <div class="d-flex align-items-center justify-content-center min-vh-100 w-100">
        <div class="row justify-content-center w-100 h-100">
            <div class="col-md-8 col-lg-6 col-xxl-3 auth-card">
                <div class="card mb-0">
                    <div class="card-body pt-5">
                        <div class="mb-5 text-center">
                            <h1 class="fs-6">Reset Password</h1>
                            <p class="mb-0 ">
                                Please enter the email address associated with your account and We will email you a link to
                                reset your password.
                            </p>
                        </div>
                        
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" id="email"
                                    aria-describedby="emailHelp" value="{{ old('email') }}">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-8 mb-3">Forgot Password</button>
                            <a href="{{route('login')}}" class="btn bg-primary-subtle text-primary w-100 py-8">Back to Login</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
