@props([
    'name' => '',
    'label' => '',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'autofocus' => false,
    'autocomplete' => 'on',
    'icon' => '',
    'disabled' => false,
    'readonly' => false,
    'class' => '',
    'class_input' => '',
    'type' => 'text',
])

<div class="form-group row mb-0 align-items-center {{ $class }}">
    @if ($label)
        <label class="text-primary text-xsm col-form-label fw-bold col-4 col-md-3 col-lg-2"
               for="{{ $name }}">{{ $label }}</label>
    @endif
    <div class="col-8 col-md-9 col-lg-10">
        <div class="input-group">
            <input @if ($required) required @endif
                   @if ($disabled) disabled @endif
                   @if ($readonly) readonly @endif
                   @if ($autofocus) autofocus @endif
                   aria-describedby="basic-addon1"
                   autocomplete="{{ $autocomplete }}"
                   class="form-control text-xsm @error($name) is-invalid @enderror {{ $class_input }}"
                   id="{{ $name }}"
                   name="{{ $name }}"
                   placeholder="{{ $placeholder }}"
                   type="{{ $type }}"
                   value="{{ $value }}">
            <button class="btn btn-toggle-password isax isax-eye-slash border"
                    type="button"></button>
            @error($name)
                <div class="invalid-feedback">
                    <i class="isax isax-info-circle"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
</div>

@push('scripts')
    <script>
        @if ($type == 'date')
            flatpickr('#{{ $name }}', {
                altInput: true,
                altFormat: "j F Y",
                dateFormat: "d-m-Y",
                defaultDate: "{{ $value }}",
            });
        @endif
    </script>
@endpush
