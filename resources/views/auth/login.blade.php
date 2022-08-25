@extends('layouts.authLayout')
@section('contents')
<div class="row justify-content-center">
    <div class="col-lg-5 col-md-7">
        <div class="card bg-secondary border-0 mb-0">
            <div class="card-body px-lg-5 py-lg-5">
                <div class="text-center text-muted mb-4">
                    <small>Sign In with credentials</small>
                </div>
                <!-- Validation Errors -->
                <x-auth-validation-errors class="mb-4 text-red" :errors="$errors" />

                @include('layouts.sections.alert-messages')
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group mb-3">
                        <div class="input-group input-group-merge input-group-alternative">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                            </div>
                            <x-input id="email" class="form-control block mt-1 w-full" type="email" name="email" placeholder="Email" :value="old('email')" required autofocus />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group input-group-merge input-group-alternative">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                            </div>
                            <x-input id="password" class="form-control block mt-1 w-full" type="password" name="password" placeholder="Password" required autocomplete="current-password" />
                            <i class="fa fa-eye" id="togglePassword" style="margin-left: -20px !important; cursor: pointer !important; margin-top: 18px; z-index: 9; padding-right: 10px;"></i>
                        </div>
                    </div>
                    <div class="custom-control custom-control-alternative custom-checkbox">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="remember">
                        <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                        </label>
                    </div>
                    <div class="text-center">
                        <x-button class="btn btn-primary my-4 ml-3">
                            {{ __('Log in') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12 text-center">
                @if (Route::has('password.request'))
                <a class="text-light" href="{{ route('password.request') }}">
                    <small>{{ __('Forgot your password?') }}</small>
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
    $(document).on('click', '#togglePassword', function(e) {
        const type = $('#password').attr('type') === 'password' ? 'text' : 'password';
        $('#password').attr('type', type);
        $(this).toggleClass('fa-eye-slash');
    });
</script>

@endsection
