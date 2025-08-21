@section('title', __($superadministrator->uid))

<x-app-layout>

    @push('styles')

    @endpush

    <x-page-title title="{{ __('Profile Edit') }}" :breadcrumbs="[['label' => __('Dashboard'), 'url' => route('dashboard')], ['label' => __($superadministrator->uid)]]"/>

    <div class="row">
        <div class="col-lg-4">

            <div class="card" id="company-view-detail">
                <div class="card-body text-center">
                    <div class="position-relative d-inline-block">
                        <div class="avatar-lg">
                            <div class="avatar-title rounded-circle">
                                <img src="{{ $superadministrator->photo != null ? Storage::disk('s3')->url('user_photo/'.$superadministrator->photo) : asset('assets/images/logo-ewallet.png') }}" alt="" class="avatar-lg rounded-circle">
                            </div>
                        </div>
                    </div>
                    <h5 class="mt-3 mb-1">{{ __($superadministrator->name) }}</h5>
                    <p class="text-muted mb-0">{{ __($superadministrator->uid) }}</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table mb-0">
                            <tbody>
                                <tr>
                                    <td class="fw-medium" scope="row">{{ __('Phone') }}</td>
                                    <td>{{ __($superadministrator->phone) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium" scope="row">{{ __('Email') }}</td>
                                    <td>{{ __($superadministrator->email) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium" scope="row">{{ __('Join Date') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($superadministrator->join_date)->format('Y-m-d') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium" scope="row">{{ __('Status') }}</td>
                                    <td><span class="badge {{ $superadministrator->status === \App\Enums\ActiveStatus::Active ? 'bg-success' : 'bg-danger' }}">{{ __(ucfirst($superadministrator->status->value)) }}</span></td>
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
                                <h5 class="card-title mb-0">{{ __($superadministrator->uid) }} {{ __('Profile Edit') }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('superadministrators.profile_update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <x-hidden-input name="user_cid" value="{{ $superadministrator->cid }}" />
                        <x-hidden-input name="version" value="{{ $superadministrator->version }}" />

                        <div class="row mb-3">
                            <div class="col-lg-4">
                                <x-text-input name="uid" id="uid" value="{{ $superadministrator->uid }}" label="{{ __('UID') }}" placeholder="{{ __('Enter UID') }}" />
                            </div>

                            <div class="col-lg-8">
                                <x-text-input name="name" id="name" value="{{ $superadministrator->name }}" label="{{ __('Name') }}" placeholder="{{ __('Enter Name') }}" />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-lg-4">
                                <x-phone-input name="phone" id="phone" value="{{ $superadministrator->phone }}" label="{{ __('Phone') }}" flag="{{ asset('assets/images/flags/my.svg') }}" country-code="+60" placeholder="{{ __('Enter Phone No. e.g. 137829842') }}" />
                            </div>

                            <div class="col-lg-8">
                                <x-email-input name="email" id="email" value="{{ $superadministrator->email }}" label="{{ __('Email') }}" placeholder="{{ __('Enter Email') }}" />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <x-password-input name="password" id="password" label="{{ __('Password') }}" placeholder="{{ __('Enter Password') }}" />
                            </div>

                            <div class="col-lg-6">
                                <x-flatpickr-input name="join_date" id="join_date" value="{{ $superadministrator->join_date }}" label="{{ __('Join Date') }}" mode="single" placeholder="{{ __('Join Date') }}" />
                            </div>
                        </div>

                        <div>
                            <x-file-input name="photo" label="{{ __('Photo') }}" accept="image/*" />
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary" id="edit-btn">{{ __('Update') }}</button>
                            <button type="reset" class="btn btn-light ms-2" id="reset-btn">{{ __('Reset') }}</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')

    @endpush

</x-app-layout>
