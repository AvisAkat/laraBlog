@extends('back.layout.auth-layout')
@section('pageTitle', isset($pageTitle) ? $pageTitle : 'Page Title Here')
@section('content')

    <div class="login-box bg-white box-shadow border-radius-10">
        <div class="login-title">
            <h2 class="text-center text-primary">Forgot Password</h2>
        </div>
        <h6 class="mb-20">
            Enter your email address to reset your password
        </h6>
        <form action="{{ route('admin.send_password_reset_link') }}" method="POST">
            <x-form-alerts></x-form-alerts>
            @csrf
            <div class="input-group custom mb-1">
                <input type="text" class="form-control form-control-lg" placeholder="Email" name="email"
                    value="{{ old('email') }}">
                <div class="input-group-append custom">
                    <span class="input-group-text"><i class="fa fa-envelope-o" aria-hidden="true"></i></span>
                </div>
            </div>
            @error('email')
                <span class="text text-danger ml-1">{{ $message }}</span>
            @enderror

            <div class="row align-items-center mt-4">
                <div class="col-12">
                    <div class="input-group mb-0">
                        <input class="btn btn-primary btn-lg btn-block" type="submit" value="Submit">
                    </div>
                </div>
                <div class="col-12 mt-2">
                    <div class="input-group mb-0">
                        <a class="btn btn-outline-primary btn-lg btn-block" href="{{ route('admin.login') }}">Login</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection