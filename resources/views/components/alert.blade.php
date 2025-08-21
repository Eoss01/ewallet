@props([
    'type' => 'success',
    'message' => null,
])

@if($message)
<div class="alert alert-{{ $type }} alert-dismissible fade show material-shadow mb-3" role="alert">
    {{ $message }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
