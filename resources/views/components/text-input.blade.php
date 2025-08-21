@props([
    'id' => null,
    'name',
    'label' => null,
    'placeholder' => '',
    'value' => '',
])

@if($label)
<label for="{{ $id ?? $name }}" class="form-label">{{ __($label) }}</label>
@endif

<input type="text" name="{{ $name }}" id="{{ $id ?? $name }}" class="form-control @error($name) is-invalid @enderror" value="{{ old($name, $value) }}" placeholder="{{ __($placeholder) }}" />

@error($name)
<div class="invalid-feedback">
    {{ $message }}
</div>
@enderror
