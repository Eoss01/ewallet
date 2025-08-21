@section('title', __('Superadministrator Dashboard'))

<x-app-layout>

    <x-page-title title="{{ __('Dashboard') }}" :breadcrumbs="[['label' => __('Dashboard')]]"/>

</x-app-layout>

@push('scripts')
<script>
    console.log("This page only");
</script>
@endpush
