@props([
    'name' => '',
    'label' => '',
    'placeholder' => '',
])

<div class="form-group mb-3">
    <div class="input-group">
        <input
               class="form-control"
               id="{{ $name }}"
               name="{{ $name }}"
               placeholder="{{ $placeholder }}"
               type="password">
        <button class="btn btn-toggle-password isax isax-eye-slash border"
                type="button"></button>
    </div>
</div>
