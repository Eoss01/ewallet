@props([
    'id' => null,
    'name',
    'label' => null,
    'multiple' => false,
    'accept' => '',  {{-- e.g. "image/*,.pdf" --}}
])

@if($label)
<label for="{{ $id ?? $name }}" class="form-label">{{ __($label) }}</label>
@endif

<input type="file" name="{{ $multiple ? $name.'[]' : $name }}" id="{{ $id ?? $name }}" class="form-control @error($name) is-invalid @enderror" {{ $multiple ? 'multiple' : '' }} {{ $accept ? 'accept='.$accept : '' }} />

@error($name)
<div class="invalid-feedback">
    {{ $message }}
</div>
@enderror
