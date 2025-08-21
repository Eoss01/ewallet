@props([
    'id' => null,
    'name',
    'label' => null,
    'value' => '',
    'placeholder' => '',
    'readonly' => false,
])

@if($label)
<label for="{{ $id ?? $name }}" class="form-label">{{ __($label) }}</label>
@endif

<div class="input-group">
    <input type="number" name="{{ $name }}" id="{{ $id ?? $name }}" class="form-control @error($name) is-invalid @enderror rounded-end flag-input" value="{{ old($name, $value) }}" placeholder="{{ __($placeholder) }}" {{ $readonly ? 'readonly' : '' }} />
</div>

@error($name)
<div class="invalid-feedback">
    {{ $message }}
</div>
@enderror
