@props([
    'id' => null,
    'name',
    'label' => null,
    'value' => '',
    'placeholder' => '',
    'mode' => 'single',
    'enableTime' => false,
    'dateFormat' => 'Y-m-d',
])

@if($label)
<label for="{{ $id ?? $name }}" class="form-label">{{ __($label) }}</label>
@endif

<input type="text" name="{{ $name }}" id="{{ $id ?? $name }}" class="form-control flatpickr-input @error($name) is-invalid @enderror" value="{{ old($name, $value) }}" placeholder="{{ __($placeholder) }}" />

@error($name)
<div class="invalid-feedback">
    {{ $message }}
</div>
@enderror

@once
    @push('styles')
    <link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    @endpush

    @push('scripts')
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script>
        $(function () {
            $(".flatpickr-input").each(function () {
                var $el = $(this);
                if (!$el.hasClass("flatpickr-applied")) {
                    $el.flatpickr({
                        mode: $el.data("mode") || "single",
                        enableTime: $el.data("enable-time") === true || $el.data("enable-time") === "true",
                        dateFormat: $el.data("date-format") || "Y-m-d"
                    });
                    $el.addClass("flatpickr-applied");
                }
            });
        });
    </script>
    @endpush
@endonce
