@section('title', __('Setting Management'))

<x-app-layout>

    <x-page-title title="{{ __('Setting Management') }}" :breadcrumbs="[['label' => __('Dashboard'), 'url' => route('dashboard')], ['label' => __('Setting Management')]]"/>

    <div class="row">
        <div class="col-lg-6">

            <x-alert type="success" :message="session('success')" />
            <x-alert type="danger" :message="session('error')" />

            <div class="card">
                <div class="card-header">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm">
                            <div>
                                <h5 class="card-title mb-0">{{ __('Platform Setting') }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body border-bottom-dashed border-bottom">

                    <form action="{{ route('settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <x-hidden-input name="setting_cid" value="{{ $setting->cid }}" />
                        <x-hidden-input name="version" value="{{ $setting->version }}" />

                        <div class="mb-3">
                            <x-number-input name="rebate_percent" id="rebate_percent" label="{{ __('Rebate Percent') }} ({{ __('%') }})" value="{{ $setting->rebate_percent }}" placeholder="{{ __('Enter Rebate Percent') }}" />
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary" id="edit-btn">{{ __('Update') }}</button>
                            <button type="reset" class="btn btn-light" id="reset-btn">{{ __('Reset') }}</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
