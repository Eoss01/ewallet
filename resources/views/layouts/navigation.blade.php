<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a href="/" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="{{ asset('assets/images/logo-ewallet.png') }}" alt="" height="40">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ asset('assets/images/logo-ewallet-bg.png') }}" alt="" height="50">
                        </span>
                    </a>

                    <a href="/" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="{{ asset('assets/images/logo-ewallet.png') }}" alt="" height="40">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ asset('assets/images/logo-ewallet-bg.png') }}" alt="" height="50">
                        </span>
                    </a>
                </div>

                <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger material-shadow-none" id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
            </div>

            <div class="d-flex align-items-center">

                <div class="dropdown ms-1 topbar-head-dropdown header-item">
                    <button type="button" class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img id="header-lang-img" src="@if(session()->get('locale') == 'en') {{ asset('assets/images/flags/us.svg') }} @elseif(session()->get('locale') == 'cn') {{ asset('assets/images/flags/china.svg') }} @elseif(session()->get('locale') == 'bm') {{ asset('assets/images/flags/my.svg') }} @endif" alt="Header Language" height="20" class="rounded">
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">

                        <a href="{{ route('change_language', ['locale' => 'en']) }}" class="dropdown-item notify-item language py-2" data-lang="en" title="English">
                            <img src="{{ asset('assets/images/flags/us.svg') }}" alt="user-image" class="me-2 rounded" height="18">
                            <span class="align-middle">{{ __('English') }}</span>
                        </a>

                        <a href="{{ route('change_language', ['locale' => 'cn']) }}" class="dropdown-item notify-item language" data-lang="cn" title="Simplified Chinese">
                            <img src="{{ asset('assets/images/flags/china.svg') }}" alt="user-image" class="me-2 rounded" height="18">
                            <span class="align-middle">{{ __('Simplified Chinese') }}</span>
                        </a>

                        <a href="{{ route('change_language', ['locale' => 'bm']) }}" class="dropdown-item notify-item language" data-lang="bm" title="Bahasa Melayu">
                            <img src="{{ asset('assets/images/flags/my.svg') }}" alt="user-image" class="me-2 rounded" height="18">
                            <span class="align-middle">{{ __('Bahasa Melayu') }}</span>
                        </a>

                    </div>
                </div>

                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn material-shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user" src="{{ Auth::user()->photo != null ? Storage::disk('s3')->url('user_photo/'.Auth::user()->photo) : asset('assets/images/logo-ewallet.png') }}" alt="Header Avatar">
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ Auth::user()->name }}</span>
                                <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">{{ Auth::user()->uid }}</span>
                            </span>
                        </span>
                    </button>

                    <div class="dropdown-menu dropdown-menu-end">
                        <h6 class="dropdown-header">{{ __('Welcome') }} {{ Auth::user()->name }}!</h6>
                        <a class="dropdown-item" href="@if(Auth::user()->hasRole('superadministrator')) {{ route('superadministrators.profile_edit', ['user_cid' => Auth::user()->cid]) }} @elseif(Auth::user()->hasRole('user')) {{ route('users.profile_edit', ['user_cid' => Auth::user()->cid]) }} @endif"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">{{ __('Profile') }}</span></a>
                        <div class="dropdown-divider"></div>
                        @if(Auth::user()->hasRole('user'))
                        <a class="dropdown-item" href="{{ route('dashboard') }}"><i class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i> <span class="align-middle">{{ __('Balance') }} : <b>{{ __('RM') }} {{ number_format(Auth::user()->wallet->wallet_balance, 2) }}</b></span></a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i>
                                <span class="align-middle">{{ __('Logout') }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
