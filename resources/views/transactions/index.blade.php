@section('title', __('Transaction Management'))

<x-app-layout>

    @push('styles')
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Sweet Alert css-->
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <!--datatable css-->
    <link href="{{ asset('assets/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <!--datatable responsive css-->
    <link href="{{ asset('assets/css/responsive.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    @endpush

    <x-page-title title="{{ __('Transaction Management') }}" :breadcrumbs="[['label' => __('Dashboard'), 'url' => route('dashboard')], ['label' => __('Transaction Management')]]"/>

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
                                <button type="button" class="btn btn-success" id="create-btn" data-bs-toggle="modal" data-bs-target="#createModal"><i class="ri-add-line align-bottom me-1"></i> {{ __('Add Transaction') }}</button>
                                @endcan
                                @can('transaction.destroy')
                                <button type="button" class="btn btn-danger" id="delete-btn"><i class="ri-delete-bin-2-line"></i> {{ __('Delete Selected') }}</button>
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
                                <th style="width: 50px;">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="check-all">
                                    </div>
                                </th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('User') }}</th>
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
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input checkbox" value="{{ $transaction->cid }}">
                                    </div>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('Y-m-d h:i A') }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xs flex-shrink-0 me-3">
                                            <img src="{{ $transaction->wallet->user->photo != null ? Storage::disk('s3')->url('user_photo/'.$transaction->wallet->user->photo) : asset('assets/images/logo-ewallet.png') }}" alt="" class="img-fluid rounded-circle">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="fs-13 mb-0"><a href="{{ route('users.show', ['user_cid' => $transaction->wallet->user->cid]) }}" class="text-body d-block" target="_blank">{{ __($transaction->wallet->user->uid) }}</a></h5>
                                        </div>
                                    </div>
                                </td>
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
            areYouSure: "{{ __('Are you sure?') }}",
            deleteRecord: "{{ __('You are about to delete this record!') }}",
            deleteSelected: "{{ __('You are about to delete selected records!') }}",
            yesDelete: "{{ __('Yes, delete it!') }}",
            yesDeleteSelected: "{{ __('Yes, delete!') }}",
            cancel: "{{ __('Cancel') }}",
            deleted: "{{ __('Deleted!') }}",
            error: "{{ __('Error!') }}",
            selectAtLeastOne: "{{ __('Please select at least one record to delete.') }}",
            noRecordSelected: "{{ __('No record selected') }}",
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

            $('#check-all').on('change', function() {
                $('.checkbox').prop('checked', $(this).prop('checked'));
            });

            $(document).on('change', '.checkbox', function() {
                $('#check-all').prop('checked', $('.checkbox:checked').length === $('.checkbox').length);
            });

            function deleteRecords(cids) {
                if(!cids.length) return;
                Swal.fire({
                    title: trans.areYouSure,
                    text: cids.length > 1 ? trans.deleteSelected : trans.deleteRecord,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f06548',
                    cancelButtonColor: '#f3f6f9',
                    cancelButtonText: trans.cancel,
                    confirmButtonText: cids.length > 1 ? trans.yesDeleteSelected : trans.yesDelete,
                    didOpen: () => {
                        const cancelBtn = Swal.getCancelButton();
                        if(cancelBtn) {
                            cancelBtn.style.color = '#000000';
                        }
                    }
                }).then((result) => {
                    if(result.isConfirmed) {
                        $.post('{{ route("transactions.destroy") }}', {
                            _token: '{{ csrf_token() }}',
                            cids: cids
                        }).done(function(response) {
                            Swal.fire(trans.deleted, response.message, 'success').then(() => location.reload());
                        }).fail(function() {
                            Swal.fire(trans.error, trans.selectAtLeastOne, 'error');
                        });
                    }
                });
            }

            $(document).on('click', '.remove-single-btn', function() {
                deleteRecords([$(this).data('cid')]);
            });

            $('#delete-btn').on('click', function() {
                let selected = $('.checkbox:checked').map(function() { return $(this).val(); }).get();
                if(selected.length === 0){
                    Swal.fire({ icon: 'warning', title: trans.noRecordSelected, text: trans.selectAtLeastOne });
                    return;
                }
                deleteRecords(selected);
            });

            $('#table').DataTable({
                columnDefs: [{ orderable: false, targets: [0, -1] }],
                order: [[1, 'desc']]
            });
        });
    </script>
    @endpush

</x-app-layout>
