@section('title', __('Superadministrator Dashboard'))

<x-app-layout>

    @push('styles')
    <!--datatable css-->
    <link href="{{ asset('assets/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <!--datatable responsive css-->
    <link href="{{ asset('assets/css/responsive.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    @endpush

    <x-page-title title="{{ __('Dashboard') }}" :breadcrumbs="[['label' => __('Dashboard')]]"/>

    <div class="row">
        <div class="col-lg-12">

            <div class="card crm-widget">
                <div class="card-body p-0">
                    <div class="row row-cols-xxl-4 row-cols-md-3 row-cols-1 g-0">
                        <div class="col">
                            <div class="py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">{{ __('Today Deposit') }}</h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ri-download-2-line display-6 text-muted cfs-22"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h2 class="mb-0 cfs-22">{{ __('RM') }} {{ number_format($today_deposits, 2) }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mt-3 mt-md-0 py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">{{ __('Today Rebate') }}</h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ri-gift-line display-6 text-muted cfs-22"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h2 class="mb-0 cfs-22">{{ __('RM') }} {{ number_format($today_rebates, 2) }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mt-3 mt-md-0 py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">{{ __('Today Withdrawal') }}</h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ri-upload-2-line display-6 text-muted cfs-22"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h2 class="mb-0 cfs-22">{{ __('RM') }} {{ number_format($today_withdrawals, 2) }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mt-3 mt-md-0 py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">{{ __('Platform Cash Flow') }}</h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ri-exchange-dollar-line display-6 text-muted cfs-22"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h2 class="mb-0 cfs-22">{{ __('RM') }} {{ number_format($total_cashflows, 2) }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-alert type="success" :message="session('success')" />
            <x-alert type="danger" :message="session('error')" />

            <div class="card">
                <div class="card-header">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm">
                            <div>
                                <h5 class="card-title mb-0">{{ __('Today Transaction List') }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body border-bottom-dashed border-bottom">
                    <form action="{{ route('dashboard.search') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-xl-6">
                                <div class="search-box">
                                    <x-text-input name="search_value" id="search_value" value="{{ $search_value }}" placeholder="{{ __('Search....') }}" />
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>

                            <div class="col-xl-6">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div>
                                            <x-select-input name="search_type" id="search_type" value="{{ $search_type }}" :options="['' => 'All Type', 'deposit' => 'Deposit', 'withdrawal' => 'Withdrawal', 'rebate' => 'Rebate']" />
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
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
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Amount') }} ({{ __('RM') }})</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($transactions as $transaction)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('Y-m-d h:i A') }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <img src="{{ $transaction->wallet->user->photo != null ? Storage::disk('s3')->url('user_photo/'.$transaction->wallet->user->photo) : asset('assets/images/logo-ewallet.png') }}" alt="" class="avatar-xxs rounded-circle image_src object-fit-cover">
                                        </div>
                                        <div class="flex-grow-1 ms-2 name"><a href="{{ route('users.show', ['user_cid' => $transaction->wallet->user->cid]) }}" class="text-body d-block">{{ __($transaction->wallet->user->uid) }}</a></div>
                                    </div>
                                </td>
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
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/select2.min.js') }}"></script>

    <script>
        $(function () {

            $('#table').DataTable({
                order: [[0, 'desc']]
            });
        });
    </script>
    @endpush

</x-app-layout>
