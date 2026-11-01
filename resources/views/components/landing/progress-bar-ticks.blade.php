@props(["values" => [0, 20, 40, 60, 80, 100]])

<div {{ $attributes->merge(["class" => "progress-bar-ticks"]) }}>
    @foreach ($values as $tick)
        <span>{{ $tick }}</span>
    @endforeach
</div>
