<x-guest-layout>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="card mt-1 card-bg-fill">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-center mt-sm-5 mb-0 text-white-50">
                                <div>
                                    <a href="/" class="d-inline-block auth-logo">
                                        <img src="{{ asset('assets/images/logo-ewallet-bg.png') }}" alt="" height="150">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="text-center mt-2">
                            <h5 class="text-primary">{{ __('Welcome Back!') }}</h5>
                            <p class="text-muted">{{ __('Sign in to continue to use EWallet.') }}</p>
                        </div>

                        <x-alert type="success" :message="session('success')" />
                        <x-alert type="danger" :message="session('error')" />

                        <div class="p-2 mt-2">
                            <form action="{{ route('login') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <x-text-input name="uid" id="uid" label="{{ __('UID') }}" placeholder="{{ __('Enter your UID') }}" />
                                </div>

                                <div class="mb-3">
                                    <x-password-input name="password" id="password" label="{{ __('Password') }}" placeholder="{{ __('Enter your password') }}" />
                                </div>

                                <div class="form-check">
                                    <input type="checkbox" name="remember" id="remember" class="form-check-input" {{ old('remember') ? 'checked' : '' }} />
                                    <label for="remember" class="form-check-label">{{ __('Remember me') }}</label>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary w-100">{{ __('Sign In') }}</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                {{-- <div class="mt-4 text-center">
                    <p class="mb-0">{{ __('Don\'t have an account ?') }} <a href="{{ route('register') }}" class="fw-semibold text-primary text-decoration-underline">{{ __('Signup') }}</a></p>
                </div> --}}
            </div>
        </div>
    </div>
</x-guest-layout>
