<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="/" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('assets/images/logo-ewallet.png') }}" alt="" height="40">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets/images/logo-ewallet-bg.png') }}" alt="" height="50">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="/" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ asset('assets/images/logo-ewallet.png') }}" alt="" height="40">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets/images/logo-ewallet-bg.png') }}" alt="" height="50">
            </span>
        </a>

        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div class="dropdown sidebar-user m-1 rounded">
        <button type="button" class="btn material-shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="d-flex align-items-center gap-2">
                <img class="rounded header-profile-user" src="{{ Auth::user()->photo ?? asset('assets/images/logo-ewallet.png') }}" alt="Header Avatar">
                <span class="text-start">
                    <span class="d-block fw-medium sidebar-user-name-text">{{ Auth::user()->name }}</span>
                    <span class="d-block fs-14 sidebar-user-name-sub-text"><i class="ri ri-circle-fill fs-10 text-success align-baseline"></i> <span class="align-middle">Online</span></span>
                </span>
            </span>
        </button>
        <div class="dropdown-menu dropdown-menu-end">
            <h6 class="dropdown-header">{{ __('Welcome') }} {{ Auth::user()->name }}!</h6>
            <a class="dropdown-item" href="@if(Auth::user()->hasRole('superadministrator')) {{ route('superadministrators.profile_edit', ['user_cid' => Auth::user()->cid]) }} @elseif(Auth::user()->hasRole('user')) {{ route('users.profile_edit', ['user_cid' => Auth::user()->cid]) }} @endif"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">{{ __('Profile') }}</span></a>
            <div class="dropdown-divider"></div>
            @if(Auth::user()->hasRole('user'))
            <a class="dropdown-item" href="pages-profile.html"><i class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i> <span class="align-middle">{{ __('Balance') }} : <b>{{ __('RM') }} {{ number_format(Auth::user()->wallet->wallet_balance) }}</b></span></a>
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

    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">{{ __('Menu') }}</span></li>

                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('dashboard') ? 'active' : ''}}" href="/">
                        <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">{{ __('Dashboard') }}</span>
                    </a>
                </li>

                @can('transaction')
                <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-finances">{{ __('Finance') }}</span></li>

                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('transactions.*') ? 'active' : ''}}" href="{{ route('transactions.index') }}">
                        <i class="ri-money-dollar-circle-line"></i> <span data-key="t-users">{{ __('Transaction') }}</span>
                    </a>
                </li>
                @endcan

                @can('user')
                <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-authentications">{{ __('Authentication') }}</span></li>

                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('users.*') ? 'active' : ''}}" href="{{ route('users.index') }}">
                        <i class="ri-account-circle-line"></i> <span data-key="t-users">{{ __('User') }}</span>
                    </a>
                </li>
                @endcan

                @can('setting')
                <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-settings">{{ __('Setting') }}</span></li>

                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('settings.*') ? 'active' : ''}}" href="{{ route('settings.index') }}">
                        <i class="ri-lock-line"></i> <span data-key="t-platform-settings">{{ __('Platform Setting') }}</span>
                    </a>
                </li>
                @endcan

            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
