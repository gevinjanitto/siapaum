@props([
    'name' => '',
    'label' => '',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'class' => '',
])

<div class="form-group row mb-0 align-items-center {{ $class }}">
    @if ($label)
        <label class="text-primary col-form-label fw-bold col-4 col-md-3 col-lg-2"
               for="{{ $name }}">{{ $label }}</label>
    @endif
    <div class="col-8 col-md-9 col-lg-10">
        <input @if ($required) required @endif
               id="{{ $name }}"
               name="{{ $name }}"
               type="hidden"
               value="{!! $value !!}">
        <trix-editor input="{{ $name }}"></trix-editor>
    </div>
    @error($name)
        <div class="invalid-feedback">
            <i class="isax isax-info-circle"></i>
            {{ $message }}
        </div>
    @enderror
</div>

@push('styles')
    <style>
        trix-toolbar [data-trix-button-group="file-tools"] {
            display: none !important;
        }

        trix-editor{
            min-height: 10em;
        }
    </style>
@endpush
