@section('title', __('User Management'))

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

    <x-page-title title="{{ __('User Management') }}" :breadcrumbs="[['label' => __('Dashboard'), 'url' => route('dashboard')], ['label' => __('User Management')]]"/>

    <div class="row">
        <div class="col-lg-12">

            <x-alert type="success" :message="session('success')" />
            <x-alert type="danger" :message="session('error')" />

            <div class="card">
                <div class="card-header">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm">
                            <div>
                                <h5 class="card-title mb-0">{{ __('User List') }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-auto">
                            <div class="d-flex flex-wrap align-items-start gap-2">
                                @can('user.create')
                                <button type="button" class="btn btn-success" id="create-btn" data-bs-toggle="modal" data-bs-target="#createModal"><i class="ri-add-line align-bottom me-1"></i> {{ __('Add User') }}</button>
                                @endcan
                                @can('user.destroy')
                                <button type="button" class="btn btn-danger" id="delete-btn"><i class="ri-delete-bin-2-line"></i> {{ __('Delete Selected') }}</button>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body border-bottom-dashed border-bottom">
                    <form action="{{ route('users.search') }}" method="GET">
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
                                            <x-select-input name="search_status" id="search_status" value="{{ $search_status }}" :options="['' => 'All Status', 'active' => 'Active', 'inactive' => 'Inactive']" />
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
                                <th>{{ __('Join Date') }}</th>
                                <th>{{ __('UID') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Wallet') }} ({{ __('RM') }})</th>
                                @can('user.edit')
                                <th>{{ __('Status') }}</th>
                                @endcan
                                @canany(['user.edit', 'user.destroy'])
                                <th>{{ __('Action') }}</th>
                                @endcan
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($users as $user)
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input checkbox" value="{{ $user->cid }}">
                                    </div>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($user->join_date)->format('Y-m-d') }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <img src="{{ $user->photo != null ? Storage::disk('s3')->url('user_photo/'.$user->photo) : asset('assets/images/logo-ewallet.png') }}" alt="" class="avatar-xxs rounded-circle image_src object-fit-cover">
                                        </div>
                                        <div class="flex-grow-1 ms-2 name"><a href="{{ route('users.show', ['user_cid' => $user->cid]) }}" class="text-body d-block">{{ __($user->uid) }}</a></div>
                                    </div>
                                </td>
                                <td>{{ __($user->name) }}</td>
                                <td><a href="mailto:{{ $user->email }}">{{ __($user->email) }}</a></td>
                                <td>{{ number_format($user->wallet->wallet_balance, 2) }}</td>
                                @can('user.edit')
                                <td>
                                    <span class="badge {{ $user->status === \App\Enums\ActiveStatus::Active ? 'bg-success' : 'bg-danger' }}">
                                        {{ __(ucfirst($user->status->value)) }}
                                    </span>
                                </td>
                                @endcan
                                @canany(['user.edit', 'user.destroy'])
                                <td>
                                    <ul class="list-inline hstack gap-2 mb-0">
                                        @can('user.edit')
                                        <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Edit">
                                            <a class="text-primary d-inline-block ms-1 edit-item-btn" data-bs-toggle="modal" data-bs-target="#editModal" data-cid="{{ $user->cid }}" data-uid="{{ $user->uid }}" data-name="{{ $user->name }}" data-phone="{{ $user->phone }}" data-email="{{ $user->email }}" data-join_date="{{ $user->join_date }}" data-status="{{ $user->status->value }}" data-photo="{{ $user->photo ? Storage::disk('s3')->url('user_photo/'.$user->photo) : '' }}" data-version="{{ $user->version }}">
                                                <i class="ri-pencil-fill fs-20"></i>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('user.destroy')
                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Remove">
                                            <a href="javascript:void(0);" class="text-danger d-inline-block ms-1 remove-single-btn" data-cid="{{ $user->cid }}">
                                                <i class="ri-delete-bin-5-fill fs-20"></i>
                                            </a>
                                        </li>
                                        @endcan
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
                    <h5 class="modal-title">{{ __('Add User') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
                </div>

                <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-body">

                        <div class="mb-3">
                            <x-text-input name="uid" id="uid" label="{{ __('UID') }}" placeholder="{{ __('Enter UID') }}" />
                        </div>

                        <div class="mb-3">
                            <x-text-input name="name" id="name" label="{{ __('Name') }}" placeholder="{{ __('Enter Name') }}" />
                        </div>

                        <div class="mb-3">
                            <x-phone-input name="phone" label="{{ __('Phone') }}" flag="{{ asset('assets/images/flags/my.svg') }}" country-code="+60" placeholder="{{ __('Enter Phone No. e.g. 137829842') }}" />
                        </div>

                        <div class="mb-3">
                            <x-email-input name="email" label="{{ __('Email') }}" placeholder="{{ __('Enter Email') }}" />
                        </div>

                        <div class="mb-3">
                            <x-password-input name="password" id="password" label="{{ __('Password') }}" placeholder="{{ __('Enter Password') }}" />
                        </div>

                        <div class="mb-3">
                            <x-flatpickr-input name="join_date" id="join_date" value="{{ \Carbon\Carbon::today(); }}" label="{{ __('Join Date') }}" mode="single" placeholder="{{ __('Join Date') }}" />
                        </div>

                        <div class="mb-3">
                            <x-file-input name="photo" label="{{ __('Photo') }}" accept="image/*" />
                        </div>

                        <div>
                            <x-select-input  name="status" id="user_status" label="{{ __('Status') }}" :options="['active' => 'Active', 'inactive' => 'Inactive']" />
                        </div>

                    </div>

                    <div class="modal-footer">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="submit" class="btn btn-success" id="add-btn">{{ __('Add User') }}</button>
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade zoomIn" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title">{{ __('Edit User') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
                </div>

                <form action="{{ route('users.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <x-hidden-input name="user_cid" />
                    <x-hidden-input name="version" />

                    <div class="modal-body">

                        <div class="mb-3">
                            <x-text-input name="edit_uid" id="edit_uid" label="{{ __('UID') }}" placeholder="{{ __('Enter UID') }}" />
                        </div>

                        <div class="mb-3">
                            <x-text-input name="edit_name" id="edit_name" label="{{ __('Name') }}" placeholder="{{ __('Enter Name') }}" />
                        </div>

                        <div class="mb-3">
                            <x-phone-input name="edit_phone" id="edit_phone" label="{{ __('Phone') }}" flag="{{ asset('assets/images/flags/my.svg') }}" country-code="+60" placeholder="{{ __('Enter Phone No. e.g. 137829842') }}" />
                        </div>

                        <div class="mb-3">
                            <x-email-input name="edit_email" id="edit_email" label="{{ __('Email') }}" placeholder="{{ __('Enter Email') }}" />
                        </div>

                        <div class="mb-3">
                            <x-password-input name="edit_password" id="edit_password" label="{{ __('Password') }}" placeholder="{{ __('(Leave blank if no change)') }}" />
                        </div>

                        <div class="mb-3">
                            <x-flatpickr-input name="edit_join_date" id="edit_join_date" label="{{ __('Join Date') }}" mode="single" placeholder="{{ __('Join Date') }}" />
                        </div>

                        <img id="edit_photo_preview" src="" alt="Photo Preview" class="img-fluid mb-3" style="display: none;">

                        <div class="mb-3">
                            <x-file-input name="edit_photo" id="edit_photo" label="{{ __('Photo') }}" accept="image/*" />
                        </div>

                        <div>
                            <x-select-input name="edit_status" id="edit_status" label="{{ __('Status') }}" :options="['active' => 'Active', 'inactive' => 'Inactive']" />
                        </div>

                    </div>

                    <div class="modal-footer">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="submit" class="btn btn-primary" id="edit-btn">{{ __('Edit User') }}</button>
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
            noRecordSelected: "{{ __('No record selected') }}"
        };
    </script>

    <script>
        $(function () {

            $(document).on('click', '.edit-item-btn', function () {
                let btn = $(this);
                $('#user_cid').val(btn.data('cid'));
                $('#version').val(btn.data('version'));
                $('#edit_uid').val(btn.data('uid'));
                $('#edit_name').val(btn.data('name'));
                $('#edit_phone').val(btn.data('phone'));
                $('#edit_email').val(btn.data('email'));
                $('#edit_join_date').val(btn.data('join_date'));
                $('#edit_status').val(btn.data('status'));
                if(btn.data('photo')) {
                    $('#edit_photo_preview').attr('src', btn.data('photo')).show();
                }
            });

            @if($errors->hasAny(['uid', 'name', 'phone', 'email', 'password', 'join_date', 'status']))
                $('#createModal').modal('show');
            @endif

            @if($errors->hasAny(['edit_uid', 'edit_name', 'edit_phone', 'edit_email', 'edit_join_date', 'edit_status']))
                $('#editModal').modal('show');
            @endif

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
                        $.post('{{ route("users.destroy") }}', {
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
