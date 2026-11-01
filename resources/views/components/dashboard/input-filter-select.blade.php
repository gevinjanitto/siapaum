@props([
    'name',
    'placeholder' => '',
    'options' => [],
    'value' => null,
    'error' => null,
    'disabled' => false,
    'readonly' => false,
    'allowClear' => false,
    'class' => '',
])

<select @if ($disabled) disabled @endif
        @if ($readonly) readonly @endif
        aria-describedby="basic-addon1"
        class="form-select @error($name) is-invalid @enderror"
        id="{{ $name }}"
        name="{{ $name }}"
        placeholder="{{ $placeholder }}">
    <option></option>
    @foreach ($options as $k => $v)
        <option @if ($value && $value == $k) selected @endif
                value="{{ $k }}">{{ $v }}</option>
    @endforeach
</select>
@error($name)
    <div class="invalid-feedback">
        <i class="isax isax-info-circle"></i>
        {{ $message }}
    </div>
@enderror

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#{{ $name }}').select2({
                placeholder: '{{ $placeholder }}',
                theme: 'bootstrap-5',
                width: '100%',
                allowClear: {{ $allowClear ? 'true' : 'false' }}
            });

            @if ($value)
                $('#{{ $name }}').val('{{ $value }}').trigger('change');
            @endif
        });
    </script>
@endpush
