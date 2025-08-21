@props([
    'id' => null,
    'name',
    'value' => '',
])

<input type="hidden" name="{{ $name }}" id="{{ $id ?? $name }}" value="{{ old($name, $value) }}" />
