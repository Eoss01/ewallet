@section('title', __('Dashboard'))

<x-app-layout>

    @push('styles')
    <!-- Sweet Alert css-->
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
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
                                <h5 class="text-muted text-uppercase fs-13">{{ __('Total Deposit') }}</h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ri-download-2-line display-6 text-muted cfs-22"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h2 class="mb-0 cfs-22">{{ __('RM') }} {{ number_format($total_deposits, 2) }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mt-3 mt-md-0 py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">{{ __('Total Rebate') }}</h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ri-gift-line display-6 text-muted cfs-22"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h2 class="mb-0 cfs-22">{{ __('RM') }} {{ number_format($total_rebates, 2) }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mt-3 mt-md-0 py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">{{ __('Total Withdrawal') }}</h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ri-upload-2-line display-6 text-muted cfs-22"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h2 class="mb-0 cfs-22">{{ __('RM') }} {{ number_format($total_withdrawals, 2) }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mt-3 mt-md-0 py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">{{ __('Wallet Balance') }}</h5>
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
                                <h5 class="card-title mb-0">{{ __('My Transaction List') }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-auto">
                            <div class="d-flex flex-wrap align-items-start gap-2">
                                @can('transaction.create')
                                <button type="button" class="btn btn-primary" id="create-deposit-btn" data-bs-toggle="modal" data-bs-target="#depositModal"><i class="ri-download-2-line align-bottom me-1"></i> {{ __('Add Deposit') }}</button>
                                <button type="button" class="btn btn-info" id="create-withdraw-btn" data-bs-toggle="modal" data-bs-target="#withdrawalModal"><i class="ri-upload-2-line align-bottom me-1"></i> {{ __('Add Withdrawal') }}</button>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body border-bottom-dashed border-bottom">
                    <form action="{{ route('dashboard.search') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-xl-12">
                                <div class="row g-3">
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
                                @can('transaction.destroy')
                                <th>{{ __('Action') }}</th>
                                @endcan
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($transactions as $transaction)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('Y-m-d h:i A') }}</td>
                                <td>{{ __(ucfirst($transaction->transaction_type->value)) }}</td>
                                <td class="text-right">{{ number_format($transaction->transaction_amount, 2) }}</td>
                                @can('transaction.destroy')
                                <td>
                                    <ul class="list-inline hstack gap-2 mb-0">
                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Remove">
                                            <a href="javascript:void(0);" class="text-danger d-inline-block ms-1 remove-single-btn" data-cid="{{ $transaction->cid }}">
                                                <i class="ri-delete-bin-5-fill fs-20"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </td>
                                @endcan
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade zoomIn" id="depositModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title">{{ __('Add Deposit') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
                </div>

                <form action="{{ route('transactions.store') }}" method="POST">
                    @csrf

                    <x-hidden-input name="user_cid" value="{{ Auth::user()->cid }}" />
                    <x-hidden-input name="transaction_type" value="deposit" />

                    <div class="modal-body">

                        <x-alert type="danger" :message="session('error_create_transaction')" />

                        <div class="mb-3">
                            <x-price-input name="current_balance" label="{{ __('Current Balance') }}" value="{{ Auth::user()->wallet->wallet_balance }}" flag="{{ asset('assets/images/flags/my.svg') }}" placeholder="{{ __('Current Balance') }}" readonly=true />
                        </div>

                        <div class="mb-3">
                            <x-price-input name="transaction_amount" label="{{ __('Amount') }}" flag="{{ asset('assets/images/flags/my.svg') }}" placeholder="{{ __('Enter Amount') }}" />
                        </div>

                        <div>
                            <x-price-input name="new_balance" label="{{ __('New Balance') }}" flag="{{ asset('assets/images/flags/my.svg') }}" placeholder="{{ __('New Balance') }}" readonly=true />
                        </div>

                    </div>

                    <div class="modal-footer">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="submit" class="btn btn-primary" id="add-btn">{{ __('Add Deposit') }}</button>
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade zoomIn" id="withdrawalModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title">{{ __('Add Withdrawal') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
                </div>

                <form action="{{ route('transactions.store') }}" method="POST">
                    @csrf

                    <x-hidden-input name="user_cid" value="{{ Auth::user()->cid }}" />
                    <x-hidden-input name="transaction_type" value="withdrawal" />

                    <div class="modal-body">

                        <x-alert type="danger" :message="session('error_create_transaction')" />

                        <div class="mb-3">
                            <x-price-input name="current_balance" label="{{ __('Current Balance') }}" value="{{ Auth::user()->wallet->wallet_balance }}" flag="{{ asset('assets/images/flags/my.svg') }}" placeholder="{{ __('Current Balance') }}" readonly=true />
                        </div>

                        <div class="mb-3">
                            <x-price-input name="transaction_amount" label="{{ __('Amount') }}" flag="{{ asset('assets/images/flags/my.svg') }}" placeholder="{{ __('Enter Amount') }}" />
                        </div>

                        <div>
                            <x-price-input name="new_balance" label="{{ __('New Balance') }}" flag="{{ asset('assets/images/flags/my.svg') }}" placeholder="{{ __('New Balance') }}" readonly=true />
                        </div>

                    </div>

                    <div class="modal-footer">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="submit" class="btn btn-info" id="add-btn">{{ __('Add Withdrawal') }}</button>
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- datatable js -->
    <script src="{{ asset('assets/js/pages/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        $(function () {

            @if(session('error_create_transaction') || $errors->hasAny(['user_cid', 'transaction_type', 'transaction_amount']))
                @if(old('transaction_type') === 'withdrawal')
                    $('#withdrawalModal').modal('show');
                @else
                    $('#depositModal').modal('show');
                @endif
            @endif

            function updateNewBalance(modal) {
                let $modal = $(modal);
                let currentBalance = parseFloat($modal.find('[name="current_balance"]').val()) || 0;
                let transactionAmount = parseFloat($modal.find('[name="transaction_amount"]').val()) || 0;
                let transactionType = $modal.find('[name="transaction_type"]').val();

                let newBalance = currentBalance;

                if (transactionType === 'deposit') {
                    newBalance = currentBalance + transactionAmount;
                } else if (transactionType === 'withdrawal') {
                    newBalance = currentBalance - transactionAmount;
                }

                $modal.find('[name="new_balance"]').val(newBalance.toFixed(2));
            }

            $('#depositModal').on('input', '[name="transaction_amount"]', function () {
                updateNewBalance('#depositModal');
            });

            $('#withdrawalModal').on('input', '[name="transaction_amount"]', function () {
                updateNewBalance('#withdrawalModal');
            });

            $('#table').DataTable({
                order: [[0, 'desc']]
            });
        });
    </script>
    @endpush

</x-app-layout>
