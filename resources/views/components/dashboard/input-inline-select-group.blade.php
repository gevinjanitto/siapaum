@props([
    'name',
    'label',
    'placeholder' => '',
    'required' => false,
    'options' => [],
    'value' => null,
    'error' => null,
    'disabled' => false,
    'readonly' => false,
    'allowClear' => false,
    'class' => '',
])

<div class="form-group row mb-0 align-items-center {{ $class }}">
    @if ($label)
        <label class="text-primary col-form-label fw-bold col-4 col-md-3 col-lg-2"
               for="{{ $name }}">{{ $label }}</label>
    @endif
    <div class="col-8 col-md-9 col-lg-10">
        <select @if ($required) required @endif
                @if ($disabled) disabled @endif
                @if ($readonly) readonly @endif
                aria-describedby="basic-addon1"
                class="form-select @error($name) is-invalid @enderror"
                id="{{ $name }}"
                name="{{ $name }}"
                placeholder="{{ $placeholder }}">
            <option value="">Pilih {{ $label }}</option>
            @foreach ($options as $groupOptions)
                <optgroup label="{{ $groupOptions['key'] }}">
                    @foreach ($groupOptions['value'] as $k => $v)
                        <option @if ($value && $value == $k) selected @endif
                                value="{{ $k }}">{{ $v }}</option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
        @error($name)
            <div class="invalid-feedback">
                <i class="isax isax-info-circle"></i>
                {{ $message }}
            </div>
        @enderror
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#{{ $name }}').select2({
                placeholder: '{{ $placeholder }}',
                theme: 'bootstrap-5',
                width: '100%',
                allowClear: {{ $allowClear ? 'true' : 'false' }},
            });

            @if ($value !== null)
                $('#{{ $name }}').val('{{ $value }}').trigger('change');
            @endif
        });
    </script>
@endpush
