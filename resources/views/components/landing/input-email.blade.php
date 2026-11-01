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
])

<div class="form-group mb-3">
    @if ($label)
        <label class="col-form-label fw-bold"
               for="{{ $name }}">{{ $label }}</label>
    @endif
    <input @if ($required) required @endif
           @if ($disabled) disabled @endif
           @if ($readonly) readonly @endif
           @if ($autofocus) autofocus @endif
           autocomplete="{{ $autocomplete }}"
           class="form-control @error($name) is-invalid @enderror"
           id="{{ $name }}"
           name="{{ $name }}"
           placeholder="{{ $placeholder }}"
           type="email"
           value="{{ $value }}">
    @error($name)
        <div class="invalid-feedback">
            <i class="isax isax-info-circle"></i>
            {{ $message }}
        </div>
    @enderror
</div>
