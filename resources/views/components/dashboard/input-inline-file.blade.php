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
    'container_id' => rand(),
    'accept' => '',
])

<div class="form-group row mb-0 align-items-center {{ $class }}"
     id="{{ $container_id }}">
    @if ($label)
        <label class="text-primary text-xsm col-form-label fw-bold col-4 col-md-3 col-lg-2"
               for="{{ $name }}">{{ $label }}</label>
    @endif
    <div class="col-8 col-md-9 col-lg-10">
        <input @if ($required) required @endif
               @if ($disabled) disabled @endif
               @if ($readonly) readonly @endif
               @if ($autofocus) autofocus @endif
               accept="{{ $accept }}"
               aria-describedby="basic-addon1"
               autocomplete="{{ $autocomplete }}"
               class="form-control text-xsm @error($name) is-invalid @enderror {{ $class_input }}"
               id="{{ $name }}"
               name="{{ $name }}"
               type="file" />
        @error($name)
            <div class="invalid-feedback">
                <i class="isax isax-info-circle"></i>
                {{ $message }}
            </div>
        @enderror
    </div>
</div>
