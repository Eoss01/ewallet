@section('title', __($user->uid))

<x-app-layout>

    @push('styles')
    <!--datatable css-->
    <link href="{{ asset('assets/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <!--datatable responsive css-->
    <link href="{{ asset('assets/css/responsive.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    @endpush

    <x-page-title title="{{ __('User Management') }}" :breadcrumbs="[['label' => __('Dashboard'), 'url' => route('dashboard')], ['label' => __('User Management'), 'url' => route('users.index')], ['label' => __($user->uid)]]"/>

    <div class="row">
        <div class="col-lg-4">

            <div class="card" id="company-view-detail">
                <div class="card-body text-center">
                    <div class="position-relative d-inline-block">
                        <div class="avatar-lg">
                            <div class="avatar-title rounded-circle">
                                <img src="{{ $user->photo != null ? Storage::disk('s3')->url('user_photo/'.$user->photo) : asset('assets/images/logo-ewallet.png') }}" alt="" class="avatar-lg rounded-circle">
                            </div>
                        </div>
                    </div>
                    <h5 class="mt-3 mb-1">{{ __($user->name) }}</h5>
                    <p class="text-muted mb-0">{{ __($user->uid) }}</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table mb-0">
                            <tbody>
                                <tr>
                                    <td class="fw-medium" scope="row">{{ __('Phone') }}</td>
                                    <td>{{ __($user->phone) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium" scope="row">{{ __('Email') }}</td>
                                    <td>{{ __($user->email) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium" scope="row">{{ __('Wallet') }}</td>
                                    <td>{{ __('RM') }} {{ number_format($user->wallet->wallet_balance, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium" scope="row">{{ __('Join Date') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($user->join_date)->format('Y-m-d') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium" scope="row">{{ __('Status') }}</td>
                                    <td><span class="badge {{ $user->status === \App\Enums\ActiveStatus::Active ? 'bg-success' : 'bg-danger' }}">{{ __(ucfirst($user->status->value)) }}</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-lg-8">

            <x-alert type="success" :message="session('success')" />
            <x-alert type="danger" :message="session('error')" />

            <div class="card">
                <div class="card-header">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm">
                            <div>
                                <h5 class="card-title mb-0">{{ __($user->uid) }} {{ __('Transaction List') }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body border-bottom-dashed border-bottom">
                    <form action="{{ route('users.show_search') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-xl-12">
                                <div class="row g-3">

                                    <x-hidden-input name="user_cid" value="{{ $user->cid }}" />

                                    <div class="col-sm-3">
                                        <div>
                                            <x-flatpickr-input name="from_date" id="from_date" value="{{ $from_date }}" mode="single" placeholder="{{ __('From Date') }}"/>
                                        </div>
                                    </div>

                                    <div class="col-sm-3">
                                        <div>
                                            <x-flatpickr-input name="to_date" id="to_date" value="{{ $to_date }}" mode="single" placeholder="{{ __('To Date') }}"/>
                                        </div>
                                    </div>

                                    <div class="col-sm-3">
                                        <div>
                                            <x-select-input name="search_type" id="search_type" value="{{ $search_type }}" :options="['' => 'All Type', 'deposit' => 'Deposit', 'withdrawal' => 'Withdrawal', 'rebate' => 'Rebate']" />
                                        </div>
                                    </div>

                                    <div class="col-sm-3">
                                        <div>
                                            <button type="submit" class="btn btn-primary w-100"> <i class="ri-equalizer-fill me-2 align-bottom"></i>{{ __('Filters') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    <table id="table" class="table table-bordered dt-responsive nowrap table-striped align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Amount') }} ({{ __('RM') }})</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($transactions as $transaction)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('Y-m-d h:i A') }}</td>
                                <td>{{ __(ucfirst($transaction->transaction_type->value)) }}</td>
                                <td class="text-right">{{ number_format($transaction->transaction_amount, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- datatable js -->
    <script src="{{ asset('assets/js/pages/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/dataTables.responsive.min.js') }}"></script>

    <script>
        $(function () {

            $('#table').DataTable({
                order: [[0, 'desc']]
            });
        });
    </script>
    @endpush

</x-app-layout>
