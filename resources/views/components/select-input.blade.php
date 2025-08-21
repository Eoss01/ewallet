@props([
    'id' => null,
    'name',
    'label' => null,
    'options' => [],
    'value' => '',
])

@if($label)
<label for="{{ $id ?? $name }}" class="form-label">{{ __($label) }}</label>
@endif

<select name="{{ $name }}" id="{{ $id ?? $name }}" class="form-control @error($name) is-invalid @enderror">
    @foreach($options as $optionValue => $optionLabel)
    <option value="{{ $optionValue }}" {{ old($name, $value) == $optionValue ? 'selected' : '' }}>{{ __($optionLabel) }}</option>
    @endforeach
</select>

@error($name)
<div class="invalid-feedback">
    {{ $message }}
</div>
@enderror
