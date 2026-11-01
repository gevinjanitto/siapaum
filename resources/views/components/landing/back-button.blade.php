@props(['label' => null])

<div {{ $attributes->merge(['class' => 'back-button-container']) }}>
    <a href="{{ route('home') }}" class="back-button">
        <i class="isax-bold isax-arrow-left"></i>
    </a>
    @if($label)
        <span class="label">{{ $label }}</span>
    @endif
</div>
