@props([
    'showBackButton' => false,
    'backButtonLabel' => null,
])

<section class="position-relative hero row g-0">
    <img src="{{ asset('assets/images/main-bg-mobile.svg') }}" alt="" class="image-main-mobile d-lg-none w-100">
    @if ($showBackButton)
        <x-landing.back-button class="back-button ms-4 my-4" />
    @endif
    <div class="col-lg-6 p-4 my-auto my-lg-0">
        <div class="h-100 content mx-auto">
            {{ $slot }}
        </div>
    </div>
</section>

@push('styles')
    <style>
        /* set backround image if width more than 990 */
        @media (min-width: 990px) {
            .hero {
                background-image: url('{{ asset('assets/images/main-bg.svg') }}');
            }
        }
    </style>

@endpush
