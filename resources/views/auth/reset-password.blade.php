@extends('layouts.authLayout')
@section('contents')

<div class="row justify-content-center">
    <div class="col-lg-5 col-md-7">
        <div class="card bg-secondary border-0 mb-0">
            <div class="card-body px-lg-5 py-lg-5">
                <div class="text-center text-muted mb-4">
                    <small>{{ __('Reset Password.') }}</small>
                </div>

                <!-- Validation Errors -->
                <x-auth-validation-errors class="mb-4" :errors="$errors" />

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div class="form-group mb-3">
                        <div class="input-group input-group-merge input-group-alternative">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                            </div>
                            <x-input id="email" class="form-control" type="email" name="email" :value="old('email', $request->email)" required autofocus />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group input-group-merge input-group-alternative">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                        </div>
                        <x-input id="password" placeholder="Password" class="form-control" type="password" name="password" required />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group input-group-merge input-group-alternative">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                            </div>
                            <x-input id="password_confirmation" class="form-control"
                                    type="password" placeholder="Confirm Password" name="password_confirmation" required />
                        </div>
                    </div>


                    <div class="text-center">
                        <x-button class="btn btn-primary my-4 ml-3">
                            {{ __('Reset Password') }}
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
                {{-- <a href="{{ route('register') }}" class="text-light"><small>Create new account</small></a> --}}
            </div>
        </div>
    </div>
</div>
@endsection