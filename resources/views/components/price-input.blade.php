@props([
    'id' => null,
    'name',
    'label' => null,
    'value' => '',
    'placeholder' => '',
    'currencyCode' => 'RM',
    'flag',
    'readonly' => false,
])

@if($label)
<label for="{{ $id ?? $name }}" class="form-label">{{ __($label) }}</label>
@endif

<div class="input-group">
    <button type="button" class="btn btn-light border">
        <img src="{{ $flag }}" alt="flag img" height="20" class="country-flagimg rounded">
        <span class="ms-2 country-codeno">{{ __($currencyCode) }}</span>
    </button>
    <input type="number" name="{{ $name }}" id="{{ $id ?? $name }}" class="form-control @error($name) is-invalid @enderror rounded-end flag-input {{ $readonly ? 'readonly-input' : '' }}" value="{{ old($name, $value) }}" placeholder="{{ __($placeholder) }}" {{ $readonly ? 'readonly' : '' }} />
</div>

@error($name)
<div class="invalid-feedback">
    {{ $message }}
</div>
@enderror
