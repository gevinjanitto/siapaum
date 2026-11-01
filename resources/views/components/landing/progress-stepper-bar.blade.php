@props([
    'steps' => [],
])

<div class="stepper-wrapper">
    @foreach ($steps as $key => $step)
        <div class="stepper-item {{ $step->get('is_done') ? 'completed fw-bold' : '' }}">
            <div class="step-counter {{ $step->get('is_done')?  'bg-primary text-white' : '' }}">{{ $loop->iteration }}
            </div>
            <div class="step-name">{{ $step->get('nama') }}</div>
            <p class="text-xsm text-center fw-normal">{{ $step->get('done_at') }}</p>
        </div>
    @endforeach
</div>
