@props([
    'id' => null,
    'name',
    'label' => null,
    'placeholder' => '',
])

@if($label)
<label for="{{ $id ?? $name }}" class="form-label">{{ __($label) }}</label>
@endif

<div class="position-relative auth-pass-inputgroup mb-3">
    <input type="password" name="{{ $name }}" id="{{ $id ?? $name }}" class="form-control @error($name) is-invalid @enderror pe-5 password-input" placeholder="{{ __($placeholder) }}" />
    <button type="button" class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon material-shadow-none" onclick="togglePassword('{{ $id ?? $name }}', this)">
        <i class="ri-eye-fill align-middle"></i>
    </button>

    @error($name)
    <div class="invalid-feedback">
        {{ $message }}
    </div>
    @enderror
</div>

@once
    @push('scripts')
    <script>
        function togglePassword(id, btn) {
            const input = document.getElementById(id);
            const icon = btn.querySelector("i");
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("ri-eye-fill");
                icon.classList.add("ri-eye-off-fill");
            } else {
                input.type = "password";
                icon.classList.remove("ri-eye-off-fill");
                icon.classList.add("ri-eye-fill");
            }
        }
    </script>
    @endpush
@endonce
