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
])

<div class="form-group row mb-0 align-items-center {{ $class }}">
    @if ($label)
        <label class="text-primary col-form-label fw-bold col-4 col-md-3 col-lg-2"
               for="{{ $name }}">{{ $label }}</label>
    @endif
    <div class="col-8 col-md-9 col-lg-10">
        <textarea @if ($required) required @endif
                  @if ($disabled) disabled @endif
                  @if ($readonly) readonly @endif
                  @if ($autofocus) autofocus @endif
                  aria-describedby="basic-addon1"
                  autocomplete="{{ $autocomplete }}"
                  class="form-control text-sm @error($name) is-invalid @enderror"
                  id="{{ $name }}"
                  name="{{ $name }}"
                  placeholder="{{ $placeholder }}">{{ $value }}</textarea>
        @error($name)
            <div class="invalid-feedback">
                <i class="isax isax-info-circle"></i>
                {{ $message }}
            </div>
        @enderror
    </div>
</div>
