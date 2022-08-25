@extends('layouts.authLayout')
@section('contents')

<div class="row justify-content-center">
    <div class="col-lg-5 col-md-7">
        <div class="card bg-secondary border-0 mb-0">
            <div class="card-body px-lg-5 py-lg-5">
                <div class="text-center text-muted mb-4">
                    <small>{{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}</small>
                </div>
                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <!-- Validation Errors -->
                <x-auth-validation-errors class="mb-4" :errors="$errors" />

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="form-group mb-3">
                        <div class="input-group input-group-merge input-group-alternative">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                            </div>
                            <x-input id="email" class="form-control" type="email" name="email" placeholder="Email" :value="old('email')" required autofocus />
                        </div>
                    </div>
                    <div class="text-center">
                        <x-button class="btn btn-primary my-4 ml-3">
                            {{ __('Email Password Reset Link') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-6">
                @if (Route::has('password.request'))
                    <a class="text-light" href="{{ route('login') }}">
                        <small>{{ __('Remember password?') }}</small>
                    </a>
                @endif
            </div>
            <div class="col-6 text-right">

            </div>
        </div>
    </div>
</div>
@endsection