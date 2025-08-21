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

            <x-alert type="success" :message="session('success')" />
            <x-alert type="danger" :message="session('error')" />

            <div class="card">
                <div class="card-header">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm">
                            <div>
                                <h5 class="card-title mb-0">{{ __('Transaction List') }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-auto">
                            <div class="d-flex flex-wrap align-items-start gap-2">
                                @can('transaction.create')
                                <button type="button" class="btn btn-primary" id="create-deposit-btn" data-bs-toggle="modal" data-bs-target="#depositModal"><i class="ri-download-2-line align-bottom me-1"></i> {{ __('Add Deposit') }}</button>
                                <button type="button" class="btn btn-info" id="create-withdraw-btn" data-bs-toggle="modal" data-bs-target="#withdrawModal"><i class="ri-upload-2-line align-bottom me-1"></i> {{ __('Add Withdraw') }}</button>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body border-bottom-dashed border-bottom">
                    <form action="{{ route('transactions.search') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-xl-4">
                                <div class="search-box">
                                    <x-text-input name="search_value" id="search_value" value="{{ $search_value }}" placeholder="{{ __('Search....') }}" />
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>

                            <div class="col-xl-8">
                                <div class="row g-3">
                                    <div class="col-sm-3">
                                        <div>
                                            <x-flatpickr-input name="from_date" id="from_date" value="{{ $from_date }}" mode="single" placeholder="{{ __('From Date') }}"/>
                                        </div>
                                    </div>

                                    <div class="col-sm-3">
                                        <div>
                                            <x-flatpickr-input name="to_date" id="to_date" value="{{ $to_date }}" mode="single" placeholder="{{ __('From Date') }}"/>
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

    <div class="modal fade zoomIn" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title">{{ __('Add Transaction') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
                </div>

                <form action="{{ route('transactions.store') }}" method="POST">
                    @csrf

                    <div class="modal-body">

                        <x-alert type="danger" :message="session('error_create_transaction')" />

                        <div class="mb-3">
                            <label for="user_cid" class="form-label">{{ __('User') }}</label>
                            <select name="user_cid" id="user_cid" class="select2 form-control @error('user_cid') is-invalid @enderror">
                                @if(old('user_cid'))
                                    <option value="{{ old('user_cid') }}" selected>{{ \App\Models\User::firstWhere('cid', old('user_cid'))->uid }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ \App\Models\User::firstWhere('cid', old('user_cid'))->name }}</option>
                                @else
                                    <option value="" selected>{{ __('-- Select --') }}</option>
                                @endif
                            </select>

                            @error('user_cid')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <x-select-input name="transaction_type" label="{{ __('Type') }}" :options="['' => '-- Select --', 'deposit' => 'Deposit', 'withdrawal' => 'Withdrawal']" />
                        </div>

                        <div class="mb-3">
                            <x-price-input name="current_balance" label="{{ __('Current Balance') }}" flag="{{ asset('assets/images/flags/my.svg') }}" placeholder="{{ __('Current Balance') }}" readonly=true />
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
                            <button type="submit" class="btn btn-success" id="add-btn">{{ __('Add Transaction') }}</button>
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
    <script src="{{ asset('assets/js/pages/select2.min.js') }}"></script>

    <!--select2 js-->
    <script src="{{ asset('assets/js/pages/select2.min.js') }}"></script>

    <script>
        const trans = {
            addDeposit: "{{ __('Add Deposit') }}",
            addWithdraw: "{{ __('Add Withdraw') }}",
        };
    </script>

    <script>
        $(function () {

            @if(session('error_create_transaction') || $errors->hasAny(['user_cid', 'transaction_type', 'transaction_amount']))
                $('#createModal').modal('show');
            @endif

            function initSelect2(selector, url, extraParams = () => ({})) {
                $(selector).select2({
                    dropdownParent: $('#createModal'),
                    ajax: {
                        url: url,
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                search: params.term,
                                page: params.page || 1,
                                ...extraParams()
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: Array.isArray(data.data) ? data.data.map(item => ({
                                    id: item.cid,
                                    text: '[' + item.uid + ']      ' + item.name,
                                    balance: item.wallet ? item.wallet.wallet_balance : 0,
                                })) : [],
                                pagination: {
                                    more: data.next_page_url !== null
                                }
                            };
                        },
                        cache: false
                    },
                    minimumInputLength: 1,
                });
            }

            let userSelect = $('#user_cid');

            if (userSelect.length) {
                initSelect2(userSelect, '/find-users');
            }

            userSelect.on('select2:select', function (e) {
                let data = e.params.data;
                let balance = parseFloat(data.balance) || 0;
                $('#current_balance').val(balance.toFixed(2));
                updateNewBalance();
            });

            $('#transaction_amount, #transaction_type').on('input', function () {
                updateNewBalance();
            });

            function updateNewBalance() {
                let currentBalance = parseFloat($('#current_balance').val()) || 0;
                let transactionAmount = parseFloat($('#transaction_amount').val()) || 0;
                let transactionType = $('#transaction_type').val();

                let newBalance = currentBalance;

                if (transactionType === 'deposit') {
                    newBalance = currentBalance + transactionAmount;
                } else if (transactionType === 'withdrawal') {
                    newBalance = currentBalance - transactionAmount;
                }

                $('#new_balance').val(newBalance.toFixed(2));
            }

            $('#table').DataTable({
                columnDefs: [{ orderable: false, targets: [-1] }],
                order: [[1, 'desc']]
            });
        });
    </script>
    @endpush

</x-app-layout>
