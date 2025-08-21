@props([
    'id' => null,
    'name',
    'label' => null,
    'options' => [],
    'value' => '',
    'ajaxUrl' => null,
    'multiple' => false,
])

<div class="mb-3">
    @if($label)
        <label for="{{ $id ?? $name }}" class="form-label">{{ __($label) }}</label>
    @endif

    <select
        name="{{ $name }}{{ $multiple ? '[]' : '' }}"
        id="{{ $id ?? $name }}"
        class="select2 form-control @error($name) is-invalid @enderror"
        {{ $multiple ? 'multiple' : '' }}
    >
        @foreach($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" {{ in_array($optionValue, (array)old($name, $value)) ? 'selected' : '' }}>
                {{ __($optionLabel) }}
            </option>
        @endforeach
    </select>

    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

@once
    @push('styles')
        <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    @endpush

    @push('scripts')
        <script src="{{ asset('assets/js/pages/select2.min.js') }}"></script>
        <script>
            function initSelect2(element, ajaxUrl = null, multiple = false, dropdownParent = null) {
                let config = {
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: '--- Select ---',
                    allowClear: true,
                };

                if (dropdownParent) {
                    config.dropdownParent = dropdownParent;
                }

                // 如果有 AJAX
                if (ajaxUrl) {
                    config.ajax = {
                        url: ajaxUrl,
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            if (!params.term || params.term.length < 2) {
                                return false; // 🛑 少於2字不查詢
                            }
                            return { search: params.term };
                        },
                        processResults: function(data) {
                            return {
                                results: data.data.map(item => ({
                                    id: item.cid,
                                    text: item.uid + ' ' + item.name
                                }))
                            };
                        },
                        cache: true
                    };
                }

                element.select2(config);

                // 初始化 old / value
                let oldValue = @json(old($name, $value));
                if (oldValue && ajaxUrl) {
                    if (!Array.isArray(oldValue)) oldValue = [oldValue];
                    $.ajax({
                        url: ajaxUrl,
                        type: 'GET',
                        data: { ids: oldValue },
                        success: function(data) {
                            let options = data.data.map(item => ({
                                id: item.cid,
                                text: item.uid + ' ' + item.name
                            }));
                            options.forEach(opt => {
                                // 🛑 避免重複 append
                                if (element.find("option[value='" + opt.id + "']").length === 0) {
                                    let newOption = new Option(opt.text, opt.id, true, true);
                                    element.append(newOption);
                                }
                            });
                            element.trigger('change');
                        }
                    });
                }
            }

            $(document).ready(function() {
                let selectElement = $('#{{ $id ?? $name }}');
                let modalParent = selectElement.closest('.modal');

                initSelect2(
                    selectElement,
                    @json($ajaxUrl),
                    @json($multiple),
                    modalParent.length ? modalParent : null
                );
            });
        </script>
    @endpush
@endonce
